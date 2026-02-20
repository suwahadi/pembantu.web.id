<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

final class Dashboard extends Component
{
    public function render()
    {
        $kpi = [
            'dispute_open' => DB::table('disputes')->whereIn('status', ['open', 'investigating'])->count(),
            'refund_queued' => DB::table('refunds')->whereIn('status', ['queued', 'processing'])->count(),
            'payout_queued' => DB::table('payouts')->whereIn('status', ['queued', 'processing'])->count(),
            'order_paid_escrow' => DB::table('orders')->where('status', 'paid_escrow')->count(),
            'order_in_progress' => DB::table('orders')->where('status', 'in_progress')->count(),
            'order_completed' => DB::table('orders')->where('status', 'completed')->count(),
        ];

        $latest = DB::table('orders')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'code', 'status', 'total_idr', 'created_at']);

        return view('livewire.admin.dashboard', compact('kpi', 'latest'));
    }
}
