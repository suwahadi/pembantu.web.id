<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Domain\Worker\Services\WorkerService;

final class WorkerForm extends Component
{
    use WithFileUploads;

    public ?int $workerId = null;

    public string $name = '';
    public ?int $categoryId = null;
    public ?int $locationId = null;
    public string $defaultScheme = 'BULANAN';
    public int $minPriceIdr = 0;
    public string $bio = '';
    public array $skillIds = [];
    public array $serviceAreaIds = [];
    public array $pricings = [];

    public $photo;
    public ?string $existingPhotoPath = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'categoryId' => ['required', 'integer'],
            'locationId' => ['required', 'integer', 'exists:locations,id'],
            'defaultScheme' => ['required', 'in:HARIAN,MINGGUAN,BULANAN,PER_JAM'],
            'minPriceIdr' => ['required', 'integer', 'min:0', 'max:999999999999'], // Max 999 juta
            'bio' => ['nullable', 'string', 'max:4000'],
            'skillIds' => ['nullable', 'array'],
            'skillIds.*' => ['integer', 'exists:service_skills,id'],
            'serviceAreaIds' => ['nullable', 'array'],
            'serviceAreaIds.*' => ['integer', 'exists:locations,id'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function mount(?int $worker = null): void
    {
        $this->workerId = $worker;

        if ($this->workerId) {
            $agency = auth()->user()->agency;
            if (!$agency) {
                return;
            }
            $agencyId = $agency->id;
            $row = DB::table('workers')->where('id', $this->workerId)->first();
            if ($row && (int)$row->agency_id === $agencyId) {
                $this->name = (string)$row->name;
                $this->categoryId = (int)$row->category_id;
                $this->locationId = $row->location_id ? (int)$row->location_id : null;
                $this->bio = (string)($row->bio ?? '');
                $this->existingPhotoPath = $row->photo_path ? (string)$row->photo_path : null;

                // Get default pricing from worker_service_pricings
                $defaultPricing = DB::table('worker_service_pricings')
                    ->where('worker_id', $this->workerId)
                    ->where('is_default', true)
                    ->first();
                $this->defaultScheme = $defaultPricing ? (string)$defaultPricing->pricing_type : 'BULANAN';
                $this->minPriceIdr = $defaultPricing ? (int)$defaultPricing->price_idr : 0;

                $this->skillIds = DB::table('worker_skills')->where('worker_id', $this->workerId)->pluck('skill_id')->toArray();
                $this->serviceAreaIds = DB::table('worker_service_areas')->where('worker_id', $this->workerId)->pluck('location_id')->toArray();
                $this->pricings = DB::table('worker_service_pricings')->where('worker_id', $this->workerId)->get()->map(fn($p) => (array)$p)->toArray();
            }
        } else {
            // Default pricing for new worker
            $this->pricings = [
                ['pricing_type' => 'daily', 'price_idr' => 0]
            ];
        }
    }

    public function save(WorkerService $workers): void
    {
        $this->validate();

        $agency = auth()->user()->agency;
        if (!$agency) {
            session()->flash('error', 'Data agensi tidak ditemukan.');
            return;
        }
        $agencyId = $agency->id;

        $photoPath = $this->existingPhotoPath;
        if ($this->photo) {
            $photoPath = $this->photo->store('workers', 'public');
        }

        try {
            if ($this->workerId) {
                $workers->update($agencyId, $this->workerId, [
                    'name' => $this->name,
                    'category_id' => $this->categoryId,
                    'location_id' => $this->locationId,
                    'bio' => $this->bio,
                    'skills' => $this->skillIds,
                    'areas' => $this->serviceAreaIds,
                    'pricings' => $this->pricings,
                    'photo_path' => $photoPath,
                ]);
                session()->flash('success', 'Worker berhasil diperbarui.');
            } else {
                $created = $workers->create($agencyId, [
                    'name' => $this->name,
                    'category_id' => $this->categoryId,
                    'location_id' => $this->locationId,
                    'bio' => $this->bio,
                    'skills' => $this->skillIds,
                    'areas' => $this->serviceAreaIds,
                    'pricings' => $this->pricings,
                    'photo_path' => $photoPath,
                    'is_active' => 1,
                ]);
                $this->workerId = (int)$created->id;
                session()->flash('success', 'Worker berhasil dibuat.');
            }

            $this->redirect(route('agency.workers.index'));
        } catch (\Throwable $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addPricing(): void
    {
        $this->pricings[] = ['pricing_type' => 'daily', 'price_idr' => 0, 'description' => ''];
    }

    public function removePricing(int $index): void
    {
        unset($this->pricings[$index]);
        $this->pricings = array_values($this->pricings);
    }

    public function removePhoto(WorkerService $workers): void
    {
        if (!$this->workerId) {
            return;
        }

        $agency = auth()->user()->agency;
        if (!$agency) {
            return;
        }

        try {
            $workers->deletePhoto($agency->id, $this->workerId);
            $this->existingPhotoPath = null;
            $this->photo = null;
            session()->flash('success', 'Foto dihapus.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $categories = DB::table('service_categories')->orderBy('name')->pluck('name', 'id')->all();
        $locations = DB::table('locations')->distinct()->select('id', 'city')->orderBy('city')->get()->mapWithKeys(fn($loc) => [$loc->id => $loc->city])->toArray();
        $allSkills = DB::table('service_skills')
            ->orderBy('name')
            ->get();

        return view('livewire.agency.worker-form', compact('categories', 'locations', 'allSkills'))
            ->layout('layouts.agency', ['title' => $this->workerId ? 'Edit Worker' : 'Tambah Worker']);
    }
}
