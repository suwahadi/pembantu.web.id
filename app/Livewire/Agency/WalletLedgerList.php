<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLedger;
use App\Models\Payout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletLedgerList extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form fields
    public bool $showPayoutForm = false;
    public int $payoutAmount = 0;
    public int $selectedBankAccountId = 0;
    public string $payoutNote = '';
    public string $statusMessage = '';
    public string $statusType = '';

    protected function rules(): array
    {
        return [
            'payoutAmount' => 'required|integer|min:10000',
            'selectedBankAccountId' => 'required|integer|exists:bank_accounts,id',
            'payoutNote' => 'nullable|string|max:255',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function togglePayoutForm(): void
    {
        $this->showPayoutForm = !$this->showPayoutForm;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->payoutAmount = 0;
        $this->selectedBankAccountId = 0;
        $this->payoutNote = '';
        $this->statusMessage = '';
        $this->statusType = '';
        $this->resetValidation();
    }

    public function submitPayoutRequest(): void
    {
        $this->validate();

        $agency = Auth::user()->agency;
        
        if (!$agency) {
            $this->statusMessage = 'Agency tidak ditemukan.';
            $this->statusType = 'error';
            $this->dispatch('payout-status-updated');
            return;
        }

        // Check if balance is sufficient
        $agencyWallet = 'agency_' . $agency->id . '_wallet';
        $credit = DB::table('wallet_ledgers')
            ->where('credit_account', $agencyWallet)
            ->sum('amount_idr');
        $debit = DB::table('wallet_ledgers')
            ->where('debit_account', $agencyWallet)
            ->sum('amount_idr');
        $balance = $credit - $debit;

        if ($this->payoutAmount > $balance) {
            $this->statusMessage = 'Saldo tidak mencukupi untuk pengajuan payout ini.';
            $this->statusType = 'error';
            $this->dispatch('payout-status-updated');
            return;
        }

        // Check bank account belongs to agency and is verified
        $bankAccount = $agency->bankAccounts()->where('id', $this->selectedBankAccountId)->where('verified_status', 'verified')->first();
        
        if (!$bankAccount) {
            $this->statusMessage = 'Rekening bank tidak valid atau belum terverifikasi.';
            $this->statusType = 'error';
            $this->dispatch('payout-status-updated');
            return;
        }

        try {
            // Create payout record
            $payout = Payout::create([
                'order_id' => 0,
                'agency_id' => $agency->id,
                'bank_account_id' => $this->selectedBankAccountId,
                'amount_idr' => $this->payoutAmount,
                'status' => 'queued',
                'notes' => $this->payoutNote,
            ]);

            $this->statusMessage = 'Pengajuan payout berhasil dikirim. Payout akan diproses dalam 1-2 hari kerja.';
            $this->statusType = 'success';
            $this->showPayoutForm = false;
            $this->resetForm();
            $this->dispatch('payout-status-updated');
        } catch (\Exception $e) {
            $this->statusMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->statusType = 'error';
            $this->dispatch('payout-status-updated');
        }
    }

    public function render()
    {
        $agency = Auth::user()->agency;
        
        if (!$agency) {
            return view('livewire.agency.wallet-ledger-list', [
                'ledgers' => collect(),
                'balance' => 0,
                'totalEarned' => 0,
                'totalPaid' => 0,
            ])->layout('layouts.agency', ['title' => 'Wallet Ledger']);
        }

        // Agency-specific accounts
        $agencyAccount = 'agency_' . $agency->id . '_payable';
        $agencyWallet = 'agency_' . $agency->id . '_wallet';

        $query = WalletLedger::query()
            ->where(function ($q) use ($agencyAccount, $agencyWallet) {
                $q->where('debit_account', $agencyAccount)
                  ->orWhere('credit_account', $agencyAccount)
                  ->orWhere('debit_account', $agencyWallet)
                  ->orWhere('credit_account', $agencyWallet);
            })
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('entry_key', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $ledgers = $query->paginate(20);

        // Calculate agency balance
        $credit = DB::table('wallet_ledgers')
            ->where('credit_account', $agencyWallet)
            ->sum('amount_idr');
        
        $debit = DB::table('wallet_ledgers')
            ->where('debit_account', $agencyWallet)
            ->sum('amount_idr');
        
        $balance = $credit - $debit;

        // Total earned (credited to payable)
        $totalEarned = DB::table('wallet_ledgers')
            ->where('credit_account', $agencyAccount)
            ->sum('amount_idr');

        // Total paid out
        $totalPaid = DB::table('wallet_ledgers')
            ->where('debit_account', $agencyWallet)
            ->where('credit_account', 'like', 'payout_%')
            ->sum('amount_idr');

        // Get verified bank accounts for payout form
        $bankAccounts = $agency->bankAccounts()
            ->where('verified_status', 'verified')
            ->get();

        return view('livewire.agency.wallet-ledger-list', [
            'ledgers' => $ledgers,
            'balance' => $balance,
            'totalEarned' => $totalEarned,
            'totalPaid' => $totalPaid,
            'agency' => $agency,
            'bankAccounts' => $bankAccounts,
        ])->layout('layouts.agency', ['title' => 'Wallet Ledger']);
    }
}
