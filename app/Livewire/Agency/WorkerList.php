<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Domain\Worker\Services\WorkerService;

final class WorkerList extends Component
{
    use WithPagination;

    public string $q = '';
    public ?int $categoryId = null;
    public ?int $locationId = null;

    protected $queryString = ['q', 'categoryId', 'locationId'];

    public function updated($name): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $workerId, WorkerService $workers): void
    {
        $agencyId = (int)auth()->user()->agency_id;

        $row = DB::table('workers')->where('id', $workerId)->first();
        if (!$row) {
            session()->flash('error', 'Worker tidak ditemukan.');
            return;
        }

        try {
            $workers->setActive($agencyId, $workerId, !(bool)$row->is_active);
            session()->flash('success', 'Status worker diperbarui.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $agencyId = (int)auth()->user()->agency_id;

        $query = DB::table('workers')
            ->join('service_categories', 'service_categories.id', '=', 'workers.category_id')
            ->leftJoin('locations', 'locations.id', '=', 'workers.location_id')
            ->select([
                'workers.id', 'workers.name', 'workers.is_active', 'workers.min_price_idr',
                'workers.default_scheme', 'workers.photo_path',
                'service_categories.name as category_name',
                DB::raw('COALESCE(locations.city, "-") as location_name'),
            ])
            ->where('workers.agency_id', $agencyId)
            ->orderByDesc('workers.id');

        if (trim($this->q) !== '') {
            $like = '%' . trim($this->q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('workers.name', 'like', $like)
                    ->orWhere('workers.skills', 'like', $like);
            });
        }
        if ($this->categoryId) {
            $query->where('workers.category_id', (int)$this->categoryId);
        }
        if ($this->locationId) {
            $query->where('workers.location_id', (int)$this->locationId);
        }

        $items = $query->paginate(15);

        $categories = DB::table('service_categories')->orderBy('name')->pluck('name', 'id')->all();
        $locations = DB::table('locations')->distinct()->select('id', 'city')->orderBy('city')->get()->mapWithKeys(fn($loc) => [$loc->id => $loc->city])->toArray();

        return view('livewire.agency.worker-list', compact('items', 'categories', 'locations'))
            ->layout('layouts.agency', ['title' => 'Workers']);
    }
}
