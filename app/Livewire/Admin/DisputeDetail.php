<?php

namespace App\Livewire\Admin;

use App\Domain\Dispute\Services\DisputeService;
use App\Domain\Shared\Statuses\DisputeStatus;
use App\Models\Dispute;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DisputeDetail extends Component
{
    public int $disputeId;

    public array $dispute = [];
    public array $statusLabels = [];

    public array $form = [
        'action' => '',
        'resolution_note' => '',
        'refund_amount_idr' => null,
        'release_amount_idr' => null,
    ];

    public bool $showResolveConfirm = false;
    public string $pendingAction = '';

    public function mount(int $disputeId): void
    {
        $this->disputeId = $disputeId;
        $this->statusLabels = DisputeStatus::labels();
        $this->loadDispute();
    }

    public function render()
    {
        return view('livewire.admin.dispute-detail')
            ->layout('layouts.admin', ['title' => 'Detail Dispute']);
    }

    public function loadDispute(): void
    {
        $dispute = Dispute::with([
            'order',
            'order.visitor',
            'order.agency',
            'openedBy',
            'resolvedBy',
            'evidences',
        ])->find($this->disputeId);

        if (!$dispute) {
            $this->dispute = [];
            return;
        }

        $this->dispute = [
            'id' => $dispute->id,
            'order_id' => $dispute->order_id,
            'status' => $dispute->status,
            'status_label' => DisputeStatus::label($dispute->status),
            'complaint' => $dispute->complaint,
            'decision' => $dispute->decision,
            'refund_amount_idr' => (int) $dispute->refund_amount_idr,
            'release_amount_idr' => (int) $dispute->release_amount_idr,
            'resolution_note' => $dispute->resolution_note,
            'created_at' => optional($dispute->created_at)->format('d M Y H:i'),
            'resolved_at' => optional($dispute->resolved_at)->format('d M Y H:i'),
            'opened_by' => $dispute->openedBy ? [
                'id' => $dispute->openedBy->id,
                'name' => $dispute->openedBy->name,
                'email' => $dispute->openedBy->email,
            ] : null,
            'resolved_by' => $dispute->resolvedBy ? [
                'id' => $dispute->resolvedBy->id,
                'name' => $dispute->resolvedBy->name,
                'email' => $dispute->resolvedBy->email,
            ] : null,
            'order' => $dispute->order ? [
                'code' => $dispute->order->code ?? ('#' . $dispute->order_id),
                'status' => $dispute->order->status,
                'total_idr' => (int) ($dispute->order->total_idr ?? 0),
                'created_at' => optional($dispute->order->created_at)->format('d M Y H:i'),
                'visitor_name' => optional($dispute->order->visitor)->name,
                'agency_name' => optional($dispute->order->agency)->company_name ?? optional($dispute->order->agency)->name,
            ] : null,
            'evidences' => $dispute->evidences->map(function ($e) {
                return [
                    'id' => $e->id,
                    'file_path' => $e->file_path,
                    'description' => $e->description,
                    'submitted_by_type' => $e->submitted_by_type,
                    'submitted_by_id' => $e->submitted_by_id,
                    'created_at' => optional($e->created_at)->format('d M Y H:i'),
                ];
            })->toArray(),
        ];

        $this->form['resolution_note'] = $this->dispute['resolution_note'] ?? '';
        $this->form['refund_amount_idr'] = $this->dispute['refund_amount_idr'] ?? null;
        $this->form['release_amount_idr'] = $this->dispute['release_amount_idr'] ?? null;
    }

    public function confirmAction(string $action): void
    {
        $this->pendingAction = $action;
        $this->showResolveConfirm = true;
    }

    public function runPendingAction(DisputeService $disputes): void
    {
        $action = $this->pendingAction;
        $this->showResolveConfirm = false;
        $this->pendingAction = '';

        if (!$action) {
            session()->flash('error', 'Aksi tidak valid.');
            return;
        }

        try {
            if ($action === 'full_refund') {
                $disputes->resolveWithFullRefund(disputeId: $this->disputeId, resolutionNote: $this->form['resolution_note']);
                session()->flash('success', 'Dispute berhasil diselesaikan dengan refund penuh.');
            } elseif ($action === 'full_release') {
                $disputes->resolveWithFullRelease(disputeId: $this->disputeId, resolutionNote: $this->form['resolution_note']);
                session()->flash('success', 'Dispute berhasil diselesaikan dengan release penuh.');
            } elseif ($action === 'partial') {
                $refund = (int) ($this->form['refund_amount_idr'] ?? 0);
                $release = (int) ($this->form['release_amount_idr'] ?? 0);

                if ($refund <= 0 && $release <= 0) {
                    session()->flash('error', 'Isi minimal salah satu jumlah (refund atau release).');
                    return;
                }

                $disputes->resolveWithPartial(
                    disputeId: $this->disputeId,
                    refundAmountIdr: $refund,
                    releaseAmountIdr: $release,
                    resolutionNote: $this->form['resolution_note']
                );
                session()->flash('success', 'Dispute berhasil diselesaikan dengan keputusan split.');
            } elseif ($action === 'reject') {
                $disputes->rejectDispute(disputeId: $this->disputeId, reason: $this->form['resolution_note']);
                session()->flash('success', 'Dispute berhasil ditolak.');
            } else {
                session()->flash('error', 'Aksi tidak dikenali.');
                return;
            }

            $this->loadDispute();
        } catch (\Throwable $e) {
            Log::error('Gagal memproses dispute', ['error' => $e->getMessage()]);
            session()->flash('error', $e->getMessage() ?: 'Terjadi kesalahan saat memproses dispute.');
        }
    }
}
