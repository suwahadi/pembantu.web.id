<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLedger;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletLedgerList extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form fields
    public bool $showRefundForm = false;
    public int $refundAmount = 0;
    public int $selectedBankAccountId = 0;
    public string $refundReason = '';
    public string $statusMessage = '';
    public string $statusType = '';

    protected function rules(): array
    {
        return [
            'refundAmount' => 'required|integer|min:10000',
            'selectedBankAccountId' => 'required|integer|exists:bank_accounts,id',
            'refundReason' => 'required|string|min:10|max:500',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleRefundForm(): void
    {
        $this->showRefundForm = !$this->showRefundForm;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->refundAmount = 0;
        $this->selectedBankAccountId = 0;
        $this->refundReason = '';
        $this->statusMessage = '';
        $this->statusType = '';
        $this->resetValidation();
    }

    public function submitRefundRequest(): void
    {
        $this->validate();

        $user = Auth::user();
        
        if (!$user) {
            $this->statusMessage = 'Silakan login terlebih dahulu.';
            $this->statusType = 'error';
            $this->dispatch('refund-status-updated');
            return;
        }

        // Check for existing pending refunds (queued or processing)
        $pendingRefund = Refund::where('payee_id', $user->id)
            ->where('payee_type', 'USER')
            ->whereIn('status', ['queued', 'processing'])
            ->first();

        if ($pendingRefund) {
            $this->statusMessage = 'Anda memiliki pengajuan refund yang masih diproses. Silakan tunggu hingga refund sebelumnya selesai diproses.';
            $this->statusType = 'error';
            $this->dispatch('refund-status-updated');
            return;
        }

        // Check if balance is sufficient
        $customerRefundable = 'customer_' . $user->id . '_refundable';
        $credit = DB::table('wallet_ledgers')
            ->where('credit_account', $customerRefundable)
            ->sum('amount_idr');
        $debit = DB::table('wallet_ledgers')
            ->where('debit_account', $customerRefundable)
            ->sum('amount_idr');
        $balance = $credit - $debit;

        if ($this->refundAmount > $balance) {
            $this->statusMessage = 'Saldo refundable tidak mencukupi untuk pengajuan refund ini.';
            $this->statusType = 'error';
            $this->dispatch('refund-status-updated');
            return;
        }

        // Check bank account belongs to user and is verified
        $bankAccount = $user->bankAccounts()->where('id', $this->selectedBankAccountId)->where('verified_status', 'verified')->first();
        
        if (!$bankAccount) {
            $this->statusMessage = 'Rekening bank tidak valid atau belum terverifikasi.';
            $this->statusType = 'error';
            $this->dispatch('refund-status-updated');
            return;
        }

        try {
            // Create refund record for wallet withdrawal (not tied to specific order)
            $refund = Refund::create([
                'order_id' => null,
                'payee_type' => 'USER',
                'payee_id' => $user->id,
                'bank_account_id' => $this->selectedBankAccountId,
                'amount_idr' => $this->refundAmount,
                'status' => 'queued',
                'reason' => $this->refundReason,
            ]);

            $this->statusMessage = 'Pengajuan refund berhasil dikirim. Refund akan diproses dalam 1-2 hari kerja.';
            $this->statusType = 'success';
            $this->showRefundForm = false;
            $this->resetForm();
            $this->dispatch('refund-status-updated');
        } catch (\Exception $e) {
            $this->statusMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->statusType = 'error';
            $this->dispatch('refund-status-updated');
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('livewire.visitor.wallet-ledger-list', [
                'ledgers' => collect(),
                'balance' => 0,
                'totalSpent' => 0,
                'totalRefunded' => 0,
                'bankAccounts' => collect(),
            ])->layout('layouts.app', ['title' => 'Wallet Ledger']);
        }

        // Visitor-specific accounts
        $customerRefundable = 'customer_' . $user->id . '_refundable';

        $query = WalletLedger::query()
            ->where(function ($q) use ($customerRefundable) {
                $q->where('debit_account', $customerRefundable)
                  ->orWhere('credit_account', $customerRefundable);
            })
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('entry_key', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $ledgers = $query->paginate(20);

        // Calculate customer refundable balance
        $credit = DB::table('wallet_ledgers')
            ->where('credit_account', $customerRefundable)
            ->sum('amount_idr');
        
        $debit = DB::table('wallet_ledgers')
            ->where('debit_account', $customerRefundable)
            ->sum('amount_idr');
        
        $balance = $credit - $debit;

        // Calculate total pending refund amount (locked balance)
        $pendingRefundAmount = Refund::where('payee_id', $user->id)
            ->where('payee_type', 'USER')
            ->whereIn('status', ['queued', 'processing'])
            ->sum('amount_idr');
        
        $availableBalance = $balance - $pendingRefundAmount;

        // Total spent (debited from refundable - means money went to escrow or other)
        $totalSpent = DB::table('wallet_ledgers')
            ->where('debit_account', $customerRefundable)
            ->where(function ($q) {
                $q->where('credit_account', 'escrow_hold')
                  ->orWhere('ref_type', 'order');
            })
            ->sum('amount_idr');

        // Total refunded (credited back)
        $totalRefunded = DB::table('wallet_ledgers')
            ->where('credit_account', $customerRefundable)
            ->where(function ($q) {
                $q->where('ref_type', 'refund')
                  ->orWhere('debit_account', 'escrow_hold');
            })
            ->sum('amount_idr');

        // Get verified bank accounts for refund form
        $bankAccounts = $user->bankAccounts()
            ->where('verified_status', 'verified')
            ->get();

        // Get user's refund history
        $refunds = Refund::where('payee_id', $user->id)
            ->where('payee_type', 'USER')
            ->with('bankAccount')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.visitor.wallet-ledger-list', [
            'ledgers' => $ledgers,
            'balance' => $balance,
            'totalSpent' => $totalSpent,
            'totalRefunded' => $totalRefunded,
            'bankAccounts' => $bankAccounts,
            'refunds' => $refunds,
            'pendingRefundAmount' => $pendingRefundAmount,
            'availableBalance' => $availableBalance,
        ])->layout('layouts.app', ['title' => 'Wallet Ledger']);
    }
}
