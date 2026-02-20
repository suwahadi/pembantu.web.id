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
        $agency = auth()->user()->agency;
        if (!$agency) {
            return;
        }
        $agencyId = $agency->id;

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
        $agency = auth()->user()->agency;
        $agencyId = $agency ? $agency->id : 0;

        $query = DB::table('workers')
            ->join('service_categories', 'service_categories.id', '=', 'workers.category_id')
            ->leftJoin('locations', 'locations.id', '=', 'workers.location_id')
            ->select([
                'workers.id', 'workers.name', 'workers.is_active', 'workers.photo_path',
                'service_categories.name as category_name',
                DB::raw('COALESCE(locations.city, "-") as location_name'),
                DB::raw('(SELECT MIN(price_idr) FROM worker_service_pricings WHERE worker_id = workers.id AND is_active = 1) as min_price'),
                DB::raw('(SELECT pricing_type FROM worker_service_pricings WHERE worker_id = workers.id AND is_default = 1 LIMIT 1) as default_scheme'),
            ])
            ->where('workers.agency_id', $agencyId)
            ->orderByDesc('workers.id');

        if (trim($this->q) !== '') {
            $like = '%' . trim($this->q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('workers.name', 'like', $like)
                    ->orWhereExists(function ($subquery) use ($like) {
                        $subquery->select(DB::raw(1))
                            ->from('worker_skills')
                            ->join('service_skills', 'service_skills.id', '=', 'worker_skills.skill_id')
                            ->where('worker_skills.worker_id', '=', DB::raw('workers.id'))
                            ->where('service_skills.name', 'like', $like);
                    });
            });
        }
        if ($this->categoryId) {
            $query->where('workers.category_id', (int)$this->categoryId);
        }
        if ($this->locationId) {
            $query->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('worker_service_areas')
                    ->where('worker_service_areas.worker_id', '=', DB::raw('workers.id'))
                    ->where('worker_service_areas.location_id', (int)$this->locationId)
                    ->where('worker_service_areas.is_active', true);
            });
        }

        $items = $query->paginate(15);

        $categories = DB::table('service_categories')->orderBy('name')->pluck('name', 'id')->all();
        $locations = DB::table('locations')->distinct()->select('id', 'city')->orderBy('city')->get()->mapWithKeys(fn($loc) => [$loc->id => $loc->city])->toArray();

        return view('livewire.agency.worker-list', compact('items', 'categories', 'locations'))
            ->layout('layouts.agency', ['title' => 'Workers']);
    }
}
