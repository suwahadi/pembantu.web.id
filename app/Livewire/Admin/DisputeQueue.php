<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Domain\Dispute\Services\DisputeService;
use App\Models\Dispute;

class DisputeQueue extends Component
{
    use WithPagination;

    public ?int $selectedDisputeId = null;
    public string $selectedAction = '';
    public string $resolutionNotes = '';
    public ?int $refundAmount = null;
    public string $status = 'open';

    public function selectDispute(int $disputeId, string $action): void
    {
        $this->selectedDisputeId = $disputeId;
        $this->selectedAction = $action;
    }

    public function resolveFullRefund(DisputeService $disputes): void
    {
        if (!$this->selectedDisputeId) {
            session()->flash('error', 'Pilih dispute terlebih dahulu.');
            return;
        }

        try {
            $disputes->resolveWithFullRefund(
                disputeId: $this->selectedDisputeId,
                resolutionNote: $this->resolutionNotes
            );

            session()->flash('success', 'Dispute diselesaikan dengan refund penuh.');
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resolveFullRelease(DisputeService $disputes): void
    {
        if (!$this->selectedDisputeId) {
            session()->flash('error', 'Pilih dispute terlebih dahulu.');
            return;
        }

        try {
            $disputes->resolveWithFullRelease(
                disputeId: $this->selectedDisputeId,
                resolutionNote: $this->resolutionNotes
            );

            session()->flash('success', 'Dispute diselesaikan dengan release penuh.');
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resolvePartial(DisputeService $disputes): void
    {
        if (!$this->selectedDisputeId || !$this->refundAmount) {
            session()->flash('error', 'Isi semua field terlebih dahulu.');
            return;
        }

        try {
            $disputes->resolveWithPartial(
                disputeId: $this->selectedDisputeId,
                refundAmountIdr: $this->refundAmount,
                releaseAmountIdr: 0,
                resolutionNote: $this->resolutionNotes
            );

            session()->flash('success', 'Dispute diselesaikan dengan split decision.');
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->selectedDisputeId = null;
        $this->selectedAction = '';
        $this->resolutionNotes = '';
        $this->refundAmount = null;
    }

    public function render()
    {
        $disputes = Dispute::whereIn('status', ['open', 'investigating'])
            ->with(['order', 'order.visitor', 'order.agency', 'evidences'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.dispute-queue', [
            'disputes' => $disputes,
        ])->layout('layouts.admin', ['title' => 'Queue Dispute']);
    }
}

