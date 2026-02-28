<?php

namespace App\Livewire\Admin;

use App\Domain\Shared\Statuses\PaymentStatus;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
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
        $query = Payment::query()->with(['order', 'order.visitor', 'order.agency']);

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('midtrans_order_id', 'like', "%{$s}%")
                    ->orWhere('transaction_id', 'like', "%{$s}%")
                    ->orWhereHas('order', function ($oq) use ($s) {
                        $oq->where('code', 'like', "%{$s}%")
                            ->orWhere('id', $s);
                    });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.payment-list', [
            'payments' => $payments,
            'statusLabels' => PaymentStatus::labels(),
        ])->layout('layouts.admin', ['title' => 'History Payment']);
    }
}
