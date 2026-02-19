<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\ServiceCategory;
use App\Models\Worker;

class Home extends Component
{
    public $categories;
    public $featured_workers;
    public $search_query = '';

    public function mount()
    {
        $this->categories = ServiceCategory::take(6)->get();
        $this->featured_workers = Worker::with([
            'category', 
            'primaryServiceArea.location',
            'defaultPricing'
        ])
            ->where('is_available', true)
            ->where('verification_status', 'verified')
            ->orderBy('rating', 'desc')
            ->orderBy('total_completed_orders', 'desc')
            ->take(8)
            ->get();
    }

    public function search()
    {
        if (!$this->search_query) {
            return redirect('/search');
        }
        return redirect('/search?q=' . urlencode($this->search_query));
    }

    public function render()
    {
        return view('livewire.pages.home')
            ->layout('layouts.app');
    }
}
