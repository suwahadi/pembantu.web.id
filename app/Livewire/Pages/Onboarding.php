<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Onboarding extends Component
{
    public $step = 1;
    public $role;

    public function mount()
    {
        if (!auth()->check()) {
            redirect()->route('login');
        }
        
        $this->role = auth()->user()->getRoleNames()->first();
    }

    public function nextStep()
    {
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function completeOnboarding()
    {
        redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.pages.onboarding', [
            'step' => $this->step,
            'role' => $this->role,
        ])->layout('layouts.app');
    }
}
