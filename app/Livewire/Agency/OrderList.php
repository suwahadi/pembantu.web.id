<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

final class OrderList extends Component
{
    use WithPagination;

    public string $status = '';
    public string $q = '';

    protected $queryString = ['status', 'q'];

    public function updated($name): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $agencyId = (int)auth()->user()->agency_id;

        $query = DB::table('orders')
            ->join('contracts', 'contracts.id', '=', 'orders.contract_id')
            ->join('workers', 'workers.id', '=', 'orders.worker_id')
            ->leftJoin('users', 'users.id', '=', 'orders.visitor_user_id')
            ->select([
                'orders.id',
                'orders.code',
                'orders.status',
                'orders.total_idr',
                'orders.created_at',
                'contracts.scheme',
                'contracts.start_date',
                'contracts.end_date',
                'workers.name as worker_name',
                'users.name as visitor_name',
            ])
            ->where('orders.agency_id', $agencyId)
            ->orderByDesc('orders.created_at');

        if ($this->status !== '') {
            $query->where('orders.status', $this->status);
        }

        if (trim($this->q) !== '') {
            $like = '%' . trim($this->q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('orders.code', 'like', $like)
                    ->orWhere('workers.name', 'like', $like)
                    ->orWhere('users.name', 'like', $like);
            });
        }

        $items = $query->paginate(15);

        $statuses = [
            'paid_escrow' => 'Paid Escrow',
            'in_progress' => 'In Progress',
            'completed_by_agency' => 'Completed by Agency',
            'completed' => 'Completed',
            'disputed' => 'Disputed',
            'cancelled' => 'Cancelled',
        ];

        return view('livewire.agency.order-list', compact('items', 'statuses'))
            ->layout('layouts.agency', ['title' => 'Orders']);
    }
}
