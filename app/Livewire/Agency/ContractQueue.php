<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Domain\Contract\Services\ContractService;

final class ContractQueue extends Component
{
    use WithPagination;

    public ?int $selectedId = null;

    public function select(int $id): void
    {
        $this->selectedId = $id;
    }

    public function sign(ContractService $contracts): void
    {
        if (!$this->selectedId) {
            return;
        }

        $agencyId = auth()->user()->agency_id;
        $contracts->signByAgency($this->selectedId, $agencyId);

        session()->flash('success', 'Kontrak berhasil ditandatangani.');
        $this->selectedId = null;
    }

    public function render()
    {
        $agencyId = auth()->user()->agency_id;

        $items = DB::table('contracts')
            ->where('agency_id', $agencyId)
            ->where('status', 'draft')
            ->orderByDesc('created_at')
            ->paginate(20);

        $selected = null;
        if ($this->selectedId) {
            $selected = DB::table('contracts')
                ->where('id', $this->selectedId)
                ->where('agency_id', $agencyId)
                ->first();
        }

        return view('livewire.agency.contract-queue', compact('items', 'selected'))
            ->layout('layouts.agency', ['title' => 'Kontrak Menunggu']);
    }
}
