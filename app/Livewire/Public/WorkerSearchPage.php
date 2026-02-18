<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Domain\Worker\Services\WorkerCatalogService;
use App\Domain\Worker\DTO\WorkerSearchDTO;

final class WorkerSearchPage extends Component
{
    use WithPagination;

    public string $q = '';
    public string $category = ''; // slug
    public string $location = ''; // slug
    public string $sort = 'relevance';

    protected $queryString = [
        'q' => ['except' => ''],
        'category' => ['except' => ''],
        'location' => ['except' => ''],
        'sort' => ['except' => 'relevance'],
    ];

    public function updated($name): void { $this->resetPage(); }

    public function render(WorkerCatalogService $catalog)
    {
        $categoryId = $this->category !== '' ? DB::table('service_categories')->where('slug', $this->category)->value('id') : null;
        $locationId = $this->location !== '' ? DB::table('locations')->where('slug', $this->location)->value('id') : null;

        $sortBy = match($this->sort) {
            'price_asc' => 'price_asc', // CatalogService might need update or I handle here
            'price_desc' => 'price_desc',
            'newest' => 'newest',
            default => 'rating',
        };

        // If CatalogService doesn't support price_asc directly, I'll handle it after results or patch CatalogService
        // Current CatalogService search():
        /*
        match($dto->sortBy) {
            'rating' => $query->orderBy('rating', 'desc'),
            'reviews' => $query->orderBy('total_reviews', 'desc'),
            'experience' => $query->orderBy('experience_years', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('rating', 'desc'),
        };
        */

        $items = $catalog->search(new WorkerSearchDTO(
            search: $this->q,
            categoryId: $categoryId,
            locationId: $locationId,
            sortBy: $this->sort, // Pass directly
        ));

        // Let's patch CatalogService to support price sorting
        
        $categories = DB::table('service_categories')->orderBy('name')->get(['name','slug']);
        $locations = DB::table('locations')->orderBy('city')->get(['city as name','slug']);

        return view('livewire.public.worker-search-page', compact('items','categories','locations'))
            ->layout('layouts.app', ['title' => 'Cari Tenaga Kerja']);
    }
}
