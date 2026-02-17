<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domain\Worker\Services\WorkerCatalogService;
use App\Models\ServiceCategory;
use App\Models\Location;

class WorkerSearch extends Component
{
    use WithPagination;

    public string $q = '';
    public ?int $categoryId = null;
    public ?int $locationId = null;
    public ?string $scheme = null;
    public ?int $maxPrice = null;
    public string $sortBy = 'rating';

    protected $queryString = ['q', 'categoryId', 'locationId', 'scheme', 'maxPrice', 'sortBy'];

    public function updated($name): void
    {
        $this->resetPage();
    }

    public function render(WorkerCatalogService $catalog)
    {
        $result = $catalog->search([
            'q' => $this->q,
            'category_id' => $this->categoryId,
            'location_id' => $this->locationId,
            'scheme' => $this->scheme,
            'max_price' => $this->maxPrice,
            'sort_by' => $this->sortBy,
            'page' => $this->getPage(),
            'per_page' => 12,
        ]);

        return view('livewire.visitor.worker-search', [
            'workers' => $result->items(),
            'paginator' => $result,
            'categories' => ServiceCategory::all()->pluck('name', 'id')->toArray(),
            'locations' => Location::whereNull('parent_id')->pluck('name', 'id')->toArray(),
            'schemes' => [
                'HARIAN' => 'Harian',
                'MINGGUAN' => 'Mingguan',
                'BULANAN' => 'Bulanan',
                'PER_JAM' => 'Per Jam',
            ],
        ])->layout('layouts.app', ['title' => 'Cari Tenaga Kerja']);
    }
}
