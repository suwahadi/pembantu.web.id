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
            ->join('orders', 'orders.id', '=', 'contracts.order_id')
            ->where('orders.agency_id', $agencyId)
            ->where('contracts.agency_signed', false)
            ->select('contracts.*')
            ->orderByDesc('contracts.created_at')
            ->paginate(20);

        $selected = null;
        if ($this->selectedId) {
            $selected = DB::table('contracts')
                ->join('orders', 'orders.id', '=', 'contracts.order_id')
                ->where('contracts.id', $this->selectedId)
                ->where('orders.agency_id', $agencyId)
                ->select('contracts.*')
                ->first();
        }

        return view('livewire.agency.contract-queue', compact('items', 'selected'))
            ->layout('layouts.agency', ['title' => 'Kontrak Menunggu']);
    }
}
