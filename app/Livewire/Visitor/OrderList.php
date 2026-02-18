<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderList extends Component
{
    public function render()
    {
        $orders = Order::where('visitor_user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('livewire.visitor.order-list', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}
