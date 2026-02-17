<?php

namespace App\Domain\Dispute\Services;

use App\Models\{Order, Dispute, DisputeEvidence, User};
use App\Domain\Shared\Statuses\{DisputeStatus, OrderStatus};
use App\Domain\Escrow\Services\EscrowService;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use App\Notifications\{OrderStatusChanged, DisputeStatusChanged};
use Illuminate\Support\Facades\{DB, Auth};

class DisputeService
{
    public function __construct(
        private EscrowService $escrowService,
        private OrderEventService $eventService,
    ) {}

    /**
     * Buka dispute oleh visitor atau agency
     */
    public function openDispute(int $orderId, string $complaint): Dispute
    {
        return DB::transaction(function () use ($orderId, $complaint) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Cek window dispute (misal 24 jam após completed atau payment settled)
            $allowedStatuses = [OrderStatus::COMPLETED, OrderStatus::PAID_ESCROW, OrderStatus::IN_PROGRESS];
            if (!in_array($order->status, $allowedStatuses)) {
                throw new \RuntimeException("Tidak bisa membuka dispute untuk status {$order->status}");
            }

            // Check if dispute already open
            if ($order->dispute) {
                throw new \RuntimeException("Sudah ada dispute yang terbuka untuk order ini");
            }

            $dispute = Dispute::create([
                'order_id' => $orderId,
                'opened_by_user_id' => Auth::id(),
                'status' => DisputeStatus::OPEN,
                'complaint' => $complaint,
            ]);

            // Update order status
            $order->update(['status' => OrderStatus::DISPUTED]);

            $this->eventService->recordStatusChange($orderId, $order->status, OrderStatus::DISPUTED, description: 'Dispute dibuka');
            AuditLogService::record('dispute_opened', 'DISPUTE', $dispute->id);

            return $dispute;
        });
    }

    /**
     * Admin add evidence ke dispute
     */
    public function addEvidence(int $disputeId, string $filePath, string $description = ''): DisputeEvidence
    {
        $dispute = Dispute::findOrFail($disputeId);

        if ($dispute->status === DisputeStatus::RESOLVED) {
            throw new \RuntimeException("Tidak bisa add evidence ke dispute yang sudah resolved");
        }

        return DisputeEvidence::create([
            'dispute_id' => $disputeId,
            'submitted_by_type' => Auth::user()->hasRole('admin') ? 'admin' : 'evidence',
            'submitted_by_id' => Auth::id(),
            'file_path' => $filePath,
            'description' => $description,
        ]);
    }

    /**
     * Admin resolve dispute - full refund
     */
    public function resolveWithFullRefund(int $disputeId, string $resolutionNote = ''): Dispute
    {
        return DB::transaction(function () use ($disputeId, $resolutionNote) {
            $dispute = Dispute::lockForUpdate()->findOrFail($disputeId);
            $order = $dispute->order()->lockForUpdate()->first();

            if ($dispute->status === DisputeStatus::RESOLVED) {
                return $dispute;
            }

            // Get escrow
            $escrow = $order->escrow;
            if (!$escrow || !$escrow->isHeld()) {
                throw new \RuntimeException("Escrow tidak tersedia atau bukan status hold");
            }

            // Update escrow ke refunded
            $this->escrowService->refundFull($order->id, "Dispute resolution - full refund");

            // Update dispute
            $dispute->update([
                'status' => DisputeStatus::RESOLVED,
                'decision' => 'full_refund',
                'refund_amount_idr' => $escrow->amount_idr,
                'release_amount_idr' => 0,
                'resolution_note' => $resolutionNote,
                'resolved_by_user_id' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // Update order
            $order->update(['status' => OrderStatus::REFUND_PENDING]);

            $this->eventService->recordStatusChange($order->id, OrderStatus::DISPUTED, OrderStatus::REFUND_PENDING, description: 'Dispute resolved - full refund');
            AuditLogService::record('dispute_resolved_full_refund', 'DISPUTE', $dispute->id);

            // Send notifications after successful commit
            DB::afterCommit(function () use ($order, $dispute) {
                // Notify visitor
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $visitor->notify(new DisputeStatusChanged(
                        disputeId: $dispute->id,
                        orderId: $order->id,
                        newStatus: DisputeStatus::RESOLVED,
                        message: 'Dispute Anda telah diselesaikan dengan keputusan refund penuh.'
                    ));
                }

                // Notify agency
                $agency = DB::table('agencies')->where('id', $order->agency_id)->first();
                if ($agency) {
                    $agencyOwner = User::find($agency->owner_user_id ?? $agency->primary_owner_user_id);
                    if ($agencyOwner) {
                        $agencyOwner->notify(new DisputeStatusChanged(
                            disputeId: $dispute->id,
                            orderId: $order->id,
                            newStatus: DisputeStatus::RESOLVED,
                            message: 'Dispute untuk order Anda telah diselesaikan dengan keputusan refund full kepada customer.'
                        ));
                    }
                }
            });

            return $dispute;
        });
    }

    /**
     * Admin resolve dispute - full release
     */
    public function resolveWithFullRelease(int $disputeId, string $resolutionNote = ''): Dispute
    {
        return DB::transaction(function () use ($disputeId, $resolutionNote) {
            $dispute = Dispute::lockForUpdate()->findOrFail($disputeId);
            $order = $dispute->order()->lockForUpdate()->first();

            if ($dispute->status === DisputeStatus::RESOLVED) {
                return $dispute;
            }

            $escrow = $order->escrow;
            if (!$escrow || !$escrow->isHeld()) {
                throw new \RuntimeException("Escrow tidak tersedia atau bukan status hold");
            }

            $this->escrowService->releaseHold($order->id, "Dispute resolution - full release");

            $dispute->update([
                'status' => DisputeStatus::RESOLVED,
                'decision' => 'full_release',
                'refund_amount_idr' => 0,
                'release_amount_idr' => $escrow->amount_idr,
                'resolution_note' => $resolutionNote,
                'resolved_by_user_id' => Auth::id(),
                'resolved_at' => now(),
            ]);

            $order->update(['status' => OrderStatus::PAYOUT_PENDING]);

            $this->eventService->recordStatusChange($order->id, OrderStatus::DISPUTED, OrderStatus::PAYOUT_PENDING, description: 'Dispute resolved - full release');
            AuditLogService::record('dispute_resolved_full_release', 'DISPUTE', $dispute->id);

            // Send notifications after successful commit
            DB::afterCommit(function () use ($order, $dispute) {
                // Notify visitor
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $visitor->notify(new DisputeStatusChanged(
                        disputeId: $dispute->id,
                        orderId: $order->id,
                        newStatus: DisputeStatus::RESOLVED,
                        message: 'Dispute Anda telah diselesaikan dengan keputusan release full ke agency.'
                    ));
                }

                // Notify agency
                $agency = DB::table('agencies')->where('id', $order->agency_id)->first();
                if ($agency) {
                    $agencyOwner = User::find($agency->owner_user_id ?? $agency->primary_owner_user_id);
                    if ($agencyOwner) {
                        $agencyOwner->notify(new DisputeStatusChanged(
                            disputeId: $dispute->id,
                            orderId: $order->id,
                            newStatus: DisputeStatus::RESOLVED,
                            message: 'Dispute untuk order Anda telah diselesaikan. Payout siap diproses.'
                        ));
                    }
                }
            });

            return $dispute;
        });
    }

    /**
     * Admin resolve dispute - partial
     */
    public function resolveWithPartial(int $disputeId, int $refundAmountIdr, int $releaseAmountIdr, string $resolutionNote = ''): Dispute
    {
        return DB::transaction(function () use ($disputeId, $refundAmountIdr, $releaseAmountIdr, $resolutionNote) {
            $dispute = Dispute::lockForUpdate()->findOrFail($disputeId);
            $order = $dispute->order()->lockForUpdate()->first();

            if ($dispute->status === DisputeStatus::RESOLVED) {
                return $dispute;
            }

            $escrow = $order->escrow;
            if (!$escrow || !$escrow->isHeld()) {
                throw new \RuntimeException("Escrow tidak tersedia atau bukan status hold");
            }

            $this->escrowService->refundPartial($order->id, $refundAmountIdr, $releaseAmountIdr, "Dispute resolution - partial");

            $dispute->update([
                'status' => DisputeStatus::RESOLVED,
                'decision' => 'partial',
                'refund_amount_idr' => $refundAmountIdr,
                'release_amount_idr' => $releaseAmountIdr,
                'resolution_note' => $resolutionNote,
                'resolved_by_user_id' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // Update order
            if ($refundAmountIdr > 0 && $releaseAmountIdr == 0) {
                $order->update(['status' => OrderStatus::REFUND_PENDING]);
            } elseif ($refundAmountIdr == 0 && $releaseAmountIdr > 0) {
                $order->update(['status' => OrderStatus::PAYOUT_PENDING]);
            } else {
                // Both exist, treat as status indicating both pending
                $order->update(['status' => OrderStatus::REFUND_PENDING]);
            }

            $this->eventService->recordStatusChange($order->id, OrderStatus::DISPUTED, $order->status, description: 'Dispute resolved - partial');
            AuditLogService::record('dispute_resolved_partial', 'DISPUTE', $dispute->id);

            // Send notifications after successful commit
            DB::afterCommit(function () use ($order, $dispute, $refundAmountIdr, $releaseAmountIdr) {
                // Notify visitor
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $message = "Dispute Anda telah diselesaikan dengan keputusan: ";
                    if ($refundAmountIdr > 0) $message .= "Refund Rp" . number_format($refundAmountIdr, 0, ',', '.') . " ";
                    if ($releaseAmountIdr > 0) $message .= "Release ke Agency Rp" . number_format($releaseAmountIdr, 0, ',', '.');
                    
                    $visitor->notify(new DisputeStatusChanged(
                        disputeId: $dispute->id,
                        orderId: $order->id,
                        newStatus: DisputeStatus::RESOLVED,
                        message: $message
                    ));
                }

                // Notify agency
                $agency = DB::table('agencies')->where('id', $order->agency_id)->first();
                if ($agency) {
                    $agencyOwner = User::find($agency->owner_user_id ?? $agency->primary_owner_user_id);
                    if ($agencyOwner) {
                        $message = "Dispute untuk order Anda telah diselesaikan dengan keputusan: ";
                        if ($refundAmountIdr > 0) $message .= "Refund ke Customer Rp" . number_format($refundAmountIdr, 0, ',', '.') . " ";
                        if ($releaseAmountIdr > 0) $message .= "Payout ke Anda Rp" . number_format($releaseAmountIdr, 0, ',', '.');
                        
                        $agencyOwner->notify(new DisputeStatusChanged(
                            disputeId: $dispute->id,
                            orderId: $order->id,
                            newStatus: DisputeStatus::RESOLVED,
                            message: $message
                        ));
                    }
                }
            });

            return $dispute;
        });
    }

    /**
     * Admin reject dispute
     */
    public function rejectDispute(int $disputeId, string $reason = ''): Dispute
    {
        return DB::transaction(function () use ($disputeId, $reason) {
            $dispute = Dispute::lockForUpdate()->findOrFail($disputeId);
            $order = $dispute->order()->lockForUpdate()->first();

            if ($dispute->status === DisputeStatus::RESOLVED || $dispute->status === DisputeStatus::REJECTED) {
                return $dispute;
            }

            $dispute->update([
                'status' => DisputeStatus::REJECTED,
                'resolution_note' => $reason,
                'resolved_by_user_id' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // Revert order status back to completed or previous
            $order->update(['status' => OrderStatus::COMPLETED]);

            $this->eventService->recordStatusChange($order->id, OrderStatus::DISPUTED, OrderStatus::COMPLETED, description: 'Dispute rejected');
            AuditLogService::record('dispute_rejected', 'DISPUTE', $dispute->id);

            return $dispute;
        });
    }

    /**
     * Get open disputes
     */
    public function getOpenDisputes()
    {
        return Dispute::where('status', DisputeStatus::OPEN)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get dispute untuk order
     */
    public function getOrderDispute(int $orderId): ?Dispute
    {
        return Dispute::where('order_id', $orderId)->first();
    }
}
