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

        $agency = auth()->user()->agency;
        if (!$agency) {
            return;
        }
        $agencyId = $agency->id;
        $contracts->agencySign($this->selectedId);

        session()->flash('success', 'Kontrak berhasil ditandatangani.');
        $this->selectedId = null;
    }

    public function render()
    {
        $agency = auth()->user()->agency;
        if (!$agency) {
            return view('livewire.agency.contract-queue', [
                'items' => collect([]),
                'selected' => null
            ])->layout('layouts.agency', ['title' => 'Kontrak Menunggu']);
        }
        $agencyId = $agency->id;

        $items = DB::table('contracts')
            ->join('orders', 'orders.id', '=', 'contracts.order_id')
            ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
            ->where('orders.agency_id', $agencyId)
            ->where('contracts.agency_signed', false)
            ->select([
                'contracts.*',
                'workers.name as worker_name'
            ])
            ->orderByDesc('contracts.created_at')
            ->paginate(20);

        // Map items to parse metadata
        $items->getCollection()->transform(function ($item) {
            $item->scheme = '-';
            if (isset($item->metadata)) {
                $meta = json_decode($item->metadata, true);
                $item->scheme = $meta['scheme'] ?? '-';
                $item->total_idr = $meta['total_price_idr'] ?? 0;
            }
            return $item;
        });

        $selected = null;
        if ($this->selectedId) {
            $selected = DB::table('contracts')
                ->join('orders', 'orders.id', '=', 'contracts.order_id')
                ->leftJoin('workers', 'workers.id', '=', 'orders.worker_id')
                ->where('contracts.id', $this->selectedId)
                ->where('orders.agency_id', $agencyId)
                ->select([
                    'contracts.*',
                    'workers.name as worker_name'
                ])
                ->first();

            if ($selected) {
                $selected->scheme = '-';
                if (isset($selected->metadata)) {
                    $meta = json_decode($selected->metadata, true);
                    $selected->scheme = $meta['scheme'] ?? '-';
                    $selected->total_idr = $meta['total_price_idr'] ?? 0;
                    $selected->estimated_hours = $meta['work_hours'] ?? null;
                    $selected->description = $meta['scope_of_work'] ?? null;
                }
            }
        }

        return view('livewire.agency.contract-queue', compact('items', 'selected'))
            ->layout('layouts.agency', ['title' => 'Kontrak Menunggu']);
    }
}
