<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Domain\Worker\Services\WorkerCatalogService;

final class WorkerShowPage extends Component
{
    public string $publicId;
    public ?object $worker = null;

    public function mount(string $publicId, WorkerCatalogService $catalog): void
    {
        $this->publicId = $publicId;
        $this->worker = $catalog->findPublicByPublicId($publicId);
        
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
