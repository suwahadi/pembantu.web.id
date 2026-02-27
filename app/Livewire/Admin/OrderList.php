<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Domain\Shared\Statuses\OrderStatus;

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
        $query = DB::table('orders')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->leftJoin('users', 'users.id', '=', 'orders.visitor_user_id')
            ->leftJoin('agencies', 'agencies.id', '=', 'orders.agency_id')
            ->select([
                'orders.id',
                'orders.code',
                'orders.status',
                'orders.total_idr',
                'orders.platform_fee_idr',
                'orders.start_date',
                'orders.end_date',
                'orders.created_at',
                'orders.completed_at',
                'orders.cancelled_at',
                'workers.name as worker_name',
                'users.name as visitor_name',
                'agencies.company_name as agency_name',
            ])
            ->whereNull('orders.deleted_at')
            ->orderByDesc('orders.created_at');

        if ($this->status !== '') {
            $query->where('orders.status', $this->status);
        }

        if (trim($this->q) !== '') {
            $like = '%' . trim($this->q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('orders.code', 'ilike', $like)
                    ->orWhere('workers.name', 'ilike', $like)
                    ->orWhere('users.name', 'ilike', $like)
                    ->orWhere('agencies.company_name', 'ilike', $like);
            });
        }

        $items = $query->paginate(20);

        $statuses = OrderStatus::labels();

        $stats = [
            'total' => DB::table('orders')->whereNull('deleted_at')->count(),
            'active' => DB::table('orders')->whereNull('deleted_at')->whereIn('status', ['paid_escrow', 'in_progress'])->count(),
            'completed' => DB::table('orders')->whereNull('deleted_at')->where('status', 'completed')->count(),
            'disputed' => DB::table('orders')->whereNull('deleted_at')->where('status', 'disputed')->count(),
        ];

        return view('livewire.admin.order-list', compact('items', 'statuses', 'stats'))
            ->layout('layouts.admin', ['title' => 'Orders']);
    }
}
