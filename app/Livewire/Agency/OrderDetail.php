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

    public function mount(int $order): void
    {
        $this->orderId = $order;
        $this->loadData();
    }

    public function loadData(): void
    {
        $agencyId = (int)auth()->user()->agency_id;

        $this->order = DB::table('orders')
            ->leftJoin('contracts', 'contracts.id', '=', 'orders.contract_id')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->leftJoin('users', 'users.id', '=', 'orders.visitor_user_id')
            ->select([
                'orders.*',
                'contracts.scheme',
                'contracts.start_date',
                'contracts.end_date',
                'workers.name as worker_name',
                'users.name as visitor_name',
            ])
            ->where('orders.id', $this->orderId)
            ->where('orders.agency_id', $agencyId)
            ->first();

        if (!$this->order) {
            session()->flash('error', 'Order tidak ditemukan atau anda tidak punya akses.');
            return;
        }

        $this->events = DB::table('order_events')
            ->where('order_id', $this->orderId)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function startJob(OrderService $orders): void
    {
        try {
            $agencyId = (int)auth()->user()->agency_id;
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
            $agencyId = (int)auth()->user()->agency_id;
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
