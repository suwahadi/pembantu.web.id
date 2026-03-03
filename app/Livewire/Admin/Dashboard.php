<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final class Dashboard extends Component
{
    public function render()
    {
        // Basic counts
        $stats = [
            'total_agencies' => DB::table('agencies')->count(),
            'total_users' => DB::table('users')->count(),
            'total_workers' => DB::table('workers')->count(),
            'total_orders' => DB::table('orders')->count(),
            'completed_orders' => DB::table('orders')->where('status', 'completed')->count(),
            'processing_orders' => DB::table('orders')->whereIn('status', ['in_progress', 'paid_escrow'])->count(),
            'pending_orders' => DB::table('orders')->where('status', 'pending_payment')->count(),
            'cancelled_orders' => DB::table('orders')->whereIn('status', ['canceled', 'cancelled', 'refunded'])->count(),
            'total_revenue' => DB::table('orders')->where('status', 'completed')->sum('total_idr'),
            'platform_fees' => DB::table('orders')->where('status', 'completed')->sum('platform_fee_idr'),
        ];

        // Growth calculations (compare with last month)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $thisMonthStart = Carbon::now()->startOfMonth();

        $lastMonthOrders = DB::table('orders')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthOrders = DB::table('orders')->where('created_at', '>=', $thisMonthStart)->count();
        $stats['orders_growth'] = $lastMonthOrders > 0 ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) : 0;

        $lastMonthAgencies = DB::table('agencies')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthAgencies = DB::table('agencies')->where('created_at', '>=', $thisMonthStart)->count();
        $stats['agencies_growth'] = $lastMonthAgencies > 0 ? round((($thisMonthAgencies - $lastMonthAgencies) / $lastMonthAgencies) * 100, 1) : 0;

        $lastMonthWorkers = DB::table('workers')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthWorkers = DB::table('workers')->where('created_at', '>=', $thisMonthStart)->count();
        $stats['workers_growth'] = $lastMonthWorkers > 0 ? round((($thisMonthWorkers - $lastMonthWorkers) / $lastMonthWorkers) * 100, 1) : 0;

        $lastMonthUsers = DB::table('users')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthUsers = DB::table('users')->where('created_at', '>=', $thisMonthStart)->count();
        $stats['users_growth'] = $lastMonthUsers > 0 ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;

        // KPI for quick actions
        $kpi = [
            'dispute_open' => DB::table('disputes')->whereIn('status', ['open', 'investigating'])->count(),
            'refund_queued' => DB::table('refunds')->whereIn('status', ['queued', 'processing'])->count(),
            'payout_queued' => DB::table('payouts')->whereIn('status', ['queued', 'processing'])->count(),
            'order_paid_escrow' => DB::table('orders')->where('status', 'paid_escrow')->count(),
            'order_in_progress' => DB::table('orders')->where('status', 'in_progress')->count(),
            'order_completed' => DB::table('orders')->where('status', 'completed')->count(),
        ];

        // Latest orders with real customer and agency names
        $latest_orders = DB::table('orders')
            ->leftJoin('users as visitors', 'orders.visitor_user_id', '=', 'visitors.id')
            ->leftJoin('agencies', 'orders.agency_id', '=', 'agencies.id')
            ->leftJoin('workers', 'orders.worker_id', '=', 'workers.id')
            ->select([
                'orders.id',
                'orders.code',
                'orders.status',
                'orders.total_idr',
                'orders.created_at',
                'visitors.name as customer_name',
                'agencies.company_name as agency_name',
                'workers.name as worker_name',
            ])
            ->orderByDesc('orders.created_at')
            ->limit(10)
            ->get();

        // Recent activities from audit_logs
        $recentActivities = DB::table('audit_logs')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                $descriptions = [
                    'order_created' => 'Pesanan baru dibuat',
                    'order_completed' => 'Pesanan selesai',
                    'payment_received' => 'Pembayaran diterima',
                    'worker_created' => 'Pekerja baru ditambahkan',
                    'agency_created' => 'Agency baru terdaftar',
                    'payout_processed' => 'Payout diproses',
                    'refund_processed' => 'Refund diproses',
                ];
                return (object) [
                    'type' => $log->action ?? 'activity',
                    'description' => $descriptions[$log->action ?? ''] ?? ($log->description ?? 'Aktivitas sistem'),
                    'metadata' => $log->metadata ?? '',
                    'created_at' => $log->created_at,
                ];
            });

        // Revenue by day for last 7 days
        $revenueData = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_idr) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'kpi', 'latest_orders', 'recentActivities', 'revenueData'));
    }
}
