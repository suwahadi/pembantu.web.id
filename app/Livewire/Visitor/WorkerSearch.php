<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domain\Worker\Services\WorkerCatalogService;
use App\Domain\Worker\DTO\WorkerSearchDTO;
use App\Models\ServiceCategory;
use App\Models\Location;

class WorkerSearch extends Component
{
    use WithPagination;

    public string $q = '';
    public ?int $categoryId = null;
    public ?int $locationId = null;
    public ?string $scheme = null;
    public string $sortBy = 'rating';

    protected $queryString = ['q', 'categoryId', 'locationId', 'scheme', 'sortBy'];

    public function updated($name): void
    {
        $this->resetPage();
    }

    public function render(WorkerCatalogService $catalog)
    {
        $dto = new WorkerSearchDTO(
            categoryId: $this->categoryId,
            locationId: $this->locationId,
            search: trim($this->q) !== '' ? $this->q : null,
            sortBy: $this->sortBy,
            perPage: 12,
        );

        $result = $catalog->search($dto);

        // Get unique cities for location filter
        $locations = Location::distinct()
            ->select('id', 'city')
            ->orderBy('city')
            ->get()
            ->mapWithKeys(fn($loc) => [$loc->id => $loc->city])
            ->toArray();

        return view('livewire.visitor.worker-search', [
            'workers' => $result->items(),
            'paginator' => $result,
            'categories' => ServiceCategory::all()->pluck('name', 'id')->toArray(),
            'locations' => $locations,
            'schemes' => [
                'HARIAN' => 'Harian',
                'MINGGUAN' => 'Mingguan',
                'BULANAN' => 'Bulanan',
                'PER_JAM' => 'Per Jam',
            ],
        ])->layout('layouts.app', ['title' => 'Cari Tenaga Kerja']);
    }
}
