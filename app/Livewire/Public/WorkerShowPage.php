<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Domain\Worker\Services\WorkerCatalogService;
use App\Models\Worker;

final class WorkerShowPage extends Component
{
    public string $publicId;
    public ?Worker $worker = null;

    public function mount(string $publicId, WorkerCatalogService $catalog): void
    {
        $this->publicId = $publicId;
        
        // Find worker by public_id first
        $workerModel = Worker::where('public_id', $publicId)
            ->where('is_available', true)
            ->where('verification_status', 'verified')
            ->first();
            
        if (!$workerModel) {
            abort(404, 'Worker tidak ditemukan atau tidak aktif.');
        }
        
        // Get detailed worker with all relationships
        $this->worker = $catalog->getDetail($workerModel->id);
        
        if (!$this->worker) {
            abort(404, 'Worker tidak ditemukan atau tidak aktif.');
        }
    }

    public function render()
    {
        return view('livewire.public.worker-show-page')
            ->layout('layouts.app', ['title' => $this->worker?->name ?? 'Detail Worker']);
    }
}
