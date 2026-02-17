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
        $this->featured_workers = Worker::with('category', 'location')
            ->where('is_featured', true)
            ->take(8)
            ->get();
    }

    public function search()
    {
        return redirect()->route('search', ['q' => $this->search_query]);
    }

    public function render()
    {
        return view('livewire.pages.home')
            ->layout('layouts.app');
    }
}
