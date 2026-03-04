<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Domain\Shared\Statuses\RefundStatus;
use App\Domain\Refund\Services\RefundService;
use App\Models\Refund;

class RefundQueue extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $selectedRefundId = null;
    public string $transferDate = '';
    public $proofFile = null;
    public string $statusFilter = 'all';
    public string $search = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function selectRefund(int $refundId): void
    {
        $this->selectedRefundId = $refundId;
        $this->transferDate = now()->toDateString();
    }

    public function markProcessing(RefundService $refunds): void
    {
        if (!$this->selectedRefundId) {
            session()->flash('error', 'Pilih refund terlebih dahulu.');
            return;
        }

        $refunds->markProcessing($this->selectedRefundId);
        session()->flash('success', 'Refund status diubah ke processing.');
        $this->resetForm();
    }

    public function markPaid(RefundService $refunds): void
    {
        if (!$this->selectedRefundId || !$this->proofFile) {
            session()->flash('error', 'Isi semua field terlebih dahulu.');
            return;
        }

        try {
            $proofPath = $this->proofFile->store('refund-proofs', 'public');

            $refunds->markPaid(
                refundId: $this->selectedRefundId,
                proofFilePath: $proofPath
            );

            session()->flash('success', 'Refund berhasil diproses.');
            $this->dispatch('refund-processed');
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->selectedRefundId = null;
        $this->transferDate = '';
        $this->proofFile = null;
    }

    public function render()
    {
        $refundsQuery = Refund::query()
            ->with(['order', 'order.visitor', 'bankAccount'])
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $refundsQuery->where('status', $this->statusFilter);
        }

        if ($this->search !== '') {
            $keyword = trim($this->search);

            $refundsQuery->where(function ($query) use ($keyword) {
                $query->where('id', 'like', '%' . $keyword . '%')
                    ->orWhere('order_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reason', 'like', '%' . $keyword . '%')
                    ->orWhere('notes', 'like', '%' . $keyword . '%')
                    ->orWhereHas('bankAccount', function ($bankQuery) use ($keyword) {
                        $bankQuery->where('account_name', 'like', '%' . $keyword . '%')
                            ->orWhere('account_no', 'like', '%' . $keyword . '%')
                            ->orWhere('bank_name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('order', function ($orderQuery) use ($keyword) {
                        $orderQuery->where('code', 'like', '%' . $keyword . '%')
                            ->orWhereHas('visitor', function ($visitorQuery) use ($keyword) {
                                $visitorQuery->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('email', 'like', '%' . $keyword . '%');
                            });
                    });
            });
        }

        $refunds = $refundsQuery->paginate(10);

        return view('livewire.admin.refund-queue', [
            'refunds' => $refunds,
        ])->layout('layouts.admin', ['title' => 'Refund Management']);
    }
}
