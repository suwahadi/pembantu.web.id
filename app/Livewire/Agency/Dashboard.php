<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use App\Models\Order;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $agency = Auth::user()->agency;
        
        $stats = [
            'total_workers' => Worker::where('agency_id', $agency?->id)->count(),
            'active_orders' => Order::whereHas('worker', function($q) use ($agency) {
                $q->where('agency_id', $agency?->id);
            })->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'total_earnings' => Order::whereHas('worker', function($q) use ($agency) {
                $q->where('agency_id', $agency?->id);
            })->where('status', 'completed')->sum('total_idr'),
        ];

        // Get latest orders for this agency
        $latest_orders = Order::whereHas('worker', function($q) use ($agency) {
            $q->where('agency_id', $agency?->id);
        })->with(['visitor', 'worker'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('livewire.agency.dashboard', [
            'stats' => $stats,
            'latest_orders' => $latest_orders
        ])->layout('layouts.agency', ['title' => 'Dashboard Agency']);
    }
}
