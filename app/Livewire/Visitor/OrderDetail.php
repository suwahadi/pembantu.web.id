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

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->order = (object) DB::table('orders')
            ->leftJoin('contracts', 'contracts.id', '=', 'orders.contract_id')
            ->leftJoin('agencies', 'agencies.id', '=', 'orders.agency_id')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->select([
                'orders.*',
                'contracts.start_date as contract_start_date',
                'contracts.end_date as contract_end_date',
                'contracts.metadata as contract_metadata',
                'agencies.company_name as agency_name',
                'workers.name as worker_name',
            ])
            ->where('orders.id', $this->orderId)
            ->first();

        if (!$this->order || !isset($this->order->id)) {
            $this->order = null;
            return;
        }

        // Extract scheme from metadata if available
        $this->order->contract_scheme = '-';
        if (isset($this->order->contract_metadata)) {
            $meta = json_decode($this->order->contract_metadata, true);
            $this->order->contract_scheme = $meta['scheme'] ?? '-';
        }

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
