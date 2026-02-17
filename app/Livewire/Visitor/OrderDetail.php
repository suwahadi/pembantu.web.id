<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domain\Order\Services\OrderService;

final class OrderDetail extends Component
{
    public int $orderId;
    public ?object $order = null;
    public array $events = [];

    public function mount(int $order): void
    {
        $this->orderId = $order;
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->order = DB::table('orders')
            ->leftJoin('contracts', 'contracts.id', '=', 'orders.contract_id')
            ->leftJoin('agencies', 'agencies.id', '=', 'orders.agency_id')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->select([
                'orders.*',
                'contracts.scheme as contract_scheme',
                'contracts.start_date',
                'contracts.end_date',
                'agencies.name as agency_name',
                'workers.name as worker_name',
            ])
            ->where('orders.id', $this->orderId)
            ->first();

        $this->events = DB::table('order_events')
            ->where('order_id', $this->orderId)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function markCompleted(OrderService $orders): void
    {
        $orders->complete($this->orderId);
        session()->flash('success', 'Order dikonfirmasi selesai.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.visitor.order-detail')
            ->layout('layouts.app', ['title' => 'Detail Pesanan']);
    }
}
