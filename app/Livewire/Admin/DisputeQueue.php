<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dispute;

class DisputeQueue extends Component
{
    use WithPagination;

    public function render()
    {
        $disputes = Dispute::with(['order', 'order.visitor', 'order.agency', 'evidences'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.dispute-queue', [
            'disputes' => $disputes,
        ])->layout('layouts.admin', ['title' => 'Queue Dispute']);
    }
}

