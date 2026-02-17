<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Domain\Payout\Services\PayoutService;
use App\Models\Payout;

class PayoutQueue extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $selectedPayoutId = null;
    public string $transferDate = '';
    public ?string $proofFile = null;
    public string $status = 'queued';

    public function selectPayout(int $payoutId): void
    {
        $this->selectedPayoutId = $payoutId;
        $this->transferDate = now()->toDateString();
    }

    public function markProcessing(PayoutService $payouts): void
    {
        if (!$this->selectedPayoutId) {
            session()->flash('error', 'Pilih payout terlebih dahulu.');
            return;
        }

        $payouts->markProcessing($this->selectedPayoutId);
        session()->flash('success', 'Payout status diubah ke processing.');
        $this->resetForm();
    }

    public function markPaid(PayoutService $payouts): void
    {
        if (!$this->selectedPayoutId || !$this->proofFile) {
            session()->flash('error', 'Isi semua field terlebih dahulu.');
            return;
        }

        try {
            $proofPath = $this->proofFile->store('payout-proofs', 'public');

            $payouts->markPaid(
                payoutId: $this->selectedPayoutId,
                proofFilePath: $proofPath
            );

            session()->flash('success', 'Payout berhasil diproses.');
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->selectedPayoutId = null;
        $this->transferDate = '';
        $this->proofFile = null;
    }

    public function render()
    {
        $payouts = Payout::whereIn('status', ['queued', 'processing'])
            ->with(['order', 'agency', 'bankAccount'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.admin.payout-queue', [
            'payouts' => $payouts,
        ])->layout('layouts.admin', ['title' => 'Queue Payout']);
    }
}

