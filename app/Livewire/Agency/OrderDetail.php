<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domain\Order\Services\OrderService;

final class OrderDetail extends Component
{
    public int $orderId;
    public ?object $order = null;
    public array $events = [];

    public string $progressNote = '';
    public $progressProof;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
        
        // Debug logging
        \Log::info('OrderDetail mounted with orderId: ' . $orderId);
        
        $this->loadData();
    }

    public function loadData(): void
    {
        $agency = auth()->user()->agency;
        $agencyId = $agency ? $agency->id : 0;
        
        // Debug logging
        \Log::info('Loading data for agency: ' . $agencyId . ', orderId: ' . $this->orderId);

        $this->order = DB::table('orders')
            ->leftJoin('contracts', 'contracts.id', '=', 'orders.contract_id')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->leftJoin('users', 'users.id', '=', 'orders.visitor_user_id')
            ->select([
                'orders.*',
                'contracts.start_date as contract_start_date',
                'contracts.end_date as contract_end_date',
                'contracts.metadata as contract_metadata',
                'workers.name as worker_name',
                'users.name as visitor_name',
            ])
            ->where('orders.id', $this->orderId)
            ->where('orders.agency_id', $agencyId)
            ->first();

        \Log::info('Order query result: ' . ($this->order ? 'Found' : 'Not found'));

        if (!$this->order) {
            session()->flash('error', 'Order tidak ditemukan atau anda tidak punya akses.');
            return;
        }

        // Extract scheme from metadata if available
        $this->order->scheme = '-';
        if (isset($this->order->contract_metadata)) {
            $meta = json_decode($this->order->contract_metadata, true);
            $this->order->scheme = $meta['scheme'] ?? '-';
        }

        $this->events = DB::table('order_events')
            ->where('order_id', $this->orderId)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
            
        \Log::info('Events loaded: ' . count($this->events));
    }

    public function startJob(OrderService $orders): void
    {
        try {
            $agency = auth()->user()->agency;
            if (!$agency) {
                return;
            }
            $agencyId = $agency->id;
            $actorUserId = (int)auth()->id();
            $orders->startJobByAgency($this->orderId, $agencyId, $actorUserId);
            session()->flash('success', 'Pekerjaan dimulai.');
            $this->loadData();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function finishJob(OrderService $orders): void
    {
        try {
            $agency = auth()->user()->agency;
            if (!$agency) {
                return;
            }
            $agencyId = $agency->id;
            $actorUserId = (int)auth()->id();
            $orders->finishJobByAgency($this->orderId, $agencyId, $actorUserId);
            session()->flash('success', 'Pekerjaan ditandai selesai.');
            $this->loadData();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agency.order-detail')
            ->layout('layouts.agency', ['title' => 'Detail Order']);
    }
}
