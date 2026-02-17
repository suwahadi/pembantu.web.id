<?php

namespace App\Livewire\Visitor;

use Livewire\Component;

class OrderDetail extends Component
{
    public $orderId;

    public function render()
    {
        return view('livewire.visitor.order-detail');
    }
}
