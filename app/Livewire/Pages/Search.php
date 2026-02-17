<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Worker;
use App\Models\ServiceCategory;
use App\Models\Location;

class Search extends Component
{
    public $query = '';
    public $category_slug = null;
    public $location_id = null;
    public $min_price = null;
    public $max_price = null;
    public $results = [];
    public $categories;
    public $locations;
    
    public function mount()
    {
        $this->query = request('q', '');
        $this->category_slug = request('category');
        $this->location_id = request('location');
        $this->min_price = request('min_price');
        $this->max_price = request('max_price');
        
        $this->categories = ServiceCategory::all();
        $this->locations = Location::all();
        
        $this->performSearch();
    }

    public function performSearch()
    {
        $query = Worker::query()
            ->with('category', 'location', 'pricings')
            ->where('is_available', true);

        // Search by name
        if ($this->query) {
            $query->where('name', 'like', '%' . $this->query . '%');
        }

        // Filter by category slug
        if ($this->category_slug) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category_slug);
            });
        }

        // Filter by location
        if ($this->location_id) {
            $query->where('location_id', $this->location_id);
        }

        // Filter by price range
        if ($this->min_price || $this->max_price) {
            $query->whereHas('pricings', function ($q) {
                if ($this->min_price) {
                    $q->where('price_idr', '>=', $this->min_price);
                }
                if ($this->max_price) {
                    $q->where('price_idr', '<=', $this->max_price);
                }
            });
        }

        $this->results = $query->orderBy('rating', 'desc')->get();
    }

    #[On('update-search')]
    public function updateSearch()
    {
        $this->performSearch();
    }

    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function updatedCategorySlug()
    {
        $this->performSearch();
    }

    public function updatedLocationId()
    {
        $this->performSearch();
    }

    public function updatedMinPrice()
    {
        $this->performSearch();
    }

    public function updatedMaxPrice()
    {
        $this->performSearch();
    }

    public function render()
    {
        return view('livewire.pages.search')
            ->layout('layouts.app');
    }
}

