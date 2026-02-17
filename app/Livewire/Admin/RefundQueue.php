<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Domain\Refund\Services\RefundService;
use App\Models\Refund;

class RefundQueue extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $selectedRefundId = null;
    public string $transferDate = '';
    public ?string $proofFile = null;
    public string $status = 'queued';

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
        $refunds = Refund::whereIn('status', ['queued', 'processing'])
            ->with(['order', 'order.visitor', 'bankAccount'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.admin.refund-queue', [
            'refunds' => $refunds,
        ])->layout('layouts.admin', ['title' => 'Queue Refund']);
    }
}

