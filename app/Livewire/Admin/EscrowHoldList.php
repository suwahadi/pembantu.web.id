<?php

namespace App\Livewire\Admin;

use App\Domain\Shared\Statuses\EscrowStatus;
use App\Models\EscrowHold;
use Livewire\Component;
use Livewire\WithPagination;

class EscrowHoldList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = EscrowHold::query()->with(['order', 'order.visitor', 'order.agency']);

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_id', $s)
                    ->orWhereHas('order', function ($oq) use ($s) {
                        $oq->where('code', 'like', "%{$s}%");
                    });
            });
        }

        $escrows = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.escrow-hold-list', [
            'escrows' => $escrows,
            'statusLabels' => EscrowStatus::labels(),
        ])->layout('layouts.admin', ['title' => 'Manajemen Escrow']);
    }
}
