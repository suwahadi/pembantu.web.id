<?php

namespace App\Livewire\Visitor;

use Livewire\Component;

class CheckoutWizard extends Component
{
    public $orderId;
    public $currentStep = 1;

    public function render()
    {
        return view('livewire.visitor.checkout-wizard');
    }
}
