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
    public string $skills = '';

    public $photo;
    public ?string $existingPhotoPath = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'categoryId' => ['required', 'integer'],
            'locationId' => ['nullable', 'integer'],
            'defaultScheme' => ['required', 'in:HARIAN,MINGGUAN,BULANAN,PER_JAM'],
            'minPriceIdr' => ['required', 'integer', 'min:0'],
            'bio' => ['nullable', 'string', 'max:4000'],
            'skills' => ['nullable', 'string', 'max:2000'],
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
                $this->defaultScheme = (string)($row->default_scheme ?? 'BULANAN');
                $this->minPriceIdr = (int)($row->min_price_idr ?? 0);
                $this->bio = (string)($row->bio ?? '');
                $this->skills = (string)($row->skills ?? '');
                $this->existingPhotoPath = $row->photo_path ? (string)$row->photo_path : null;
            }
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
                    'default_scheme' => $this->defaultScheme,
                    'min_price_idr' => $this->minPriceIdr,
                    'bio' => $this->bio,
                    'skills' => $this->skills,
                    'photo_path' => $photoPath,
                ]);
                session()->flash('success', 'Worker berhasil diperbarui.');
            } else {
                $created = $workers->create($agencyId, [
                    'name' => $this->name,
                    'category_id' => $this->categoryId,
                    'location_id' => $this->locationId,
                    'default_scheme' => $this->defaultScheme,
                    'min_price_idr' => $this->minPriceIdr,
                    'bio' => $this->bio,
                    'skills' => $this->skills,
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

        return view('livewire.agency.worker-form', compact('categories', 'locations'))
            ->layout('layouts.agency', ['title' => $this->workerId ? 'Edit Worker' : 'Tambah Worker']);
    }
}
