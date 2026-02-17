<?php

namespace App\Livewire\Visitor;

use Livewire\Component;

class WorkerSearch extends Component
{
    public $categoryId = null;
    public $locationId = null;
    public $minRating = null;
    public $search = '';
    public $sortBy = 'rating';

    public function render()
    {
        return view('livewire.visitor.worker-search');
    }
}
