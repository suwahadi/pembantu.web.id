<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\DB;

class WalletLedgerList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $accountFilter = '';
    public string $refTypeFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRefTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WalletLedger::query()
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('entry_key', 'like', '%' . $this->search . '%')
                  ->orWhere('debit_account', 'like', '%' . $this->search . '%')
                  ->orWhere('credit_account', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->accountFilter) {
            $query->where(function ($q) {
                $q->where('debit_account', $this->accountFilter)
                  ->orWhere('credit_account', $this->accountFilter);
            });
        }

        if ($this->refTypeFilter) {
            $query->where('ref_type', $this->refTypeFilter);
        }

        $ledgers = $query->paginate(20);

        // Get unique accounts for filter
        $accounts = DB::table('wallet_ledgers')
            ->select('debit_account')
            ->distinct()
            ->pluck('debit_account')
            ->merge(
                DB::table('wallet_ledgers')
                    ->select('credit_account')
                    ->distinct()
                    ->pluck('credit_account')
            )
            ->unique()
            ->sort()
            ->values();

        // Get unique ref types
        $refTypes = DB::table('wallet_ledgers')
            ->select('ref_type')
            ->distinct()
            ->pluck('ref_type');

        // Calculate totals
        $totalDebit = DB::table('wallet_ledgers')->sum('amount_idr');
        $totalCount = DB::table('wallet_ledgers')->count();

        return view('livewire.admin.wallet-ledger-list', [
            'ledgers' => $ledgers,
            'accounts' => $accounts,
            'refTypes' => $refTypes,
            'totalDebit' => $totalDebit,
            'totalCount' => $totalCount,
        ])->layout('layouts.admin', ['title' => 'Wallet Ledger']);
    }
}
