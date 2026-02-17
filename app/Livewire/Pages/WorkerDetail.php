<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Worker;

class WorkerDetail extends Component
{
    public $worker;
    public $worker_id;

    public function mount($id)
    {
        $this->worker = Worker::with('category', 'location', 'pricings')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.pages.worker-detail', [
            'worker' => $this->worker,
        ])->layout('layouts.app');
    }
}
