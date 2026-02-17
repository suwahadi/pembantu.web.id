<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domain\Bank\Services\BankAccountService;

final class BankAccounts extends Component
{
    public string $bankName = '';
    public string $accountNo = '';
    public string $accountName = '';
    public ?int $primaryId = null;

    protected function rules(): array
    {
        return [
            'bankName' => ['required', 'string', 'min:2', 'max:50'],
            'accountNo' => ['required', 'string', 'min:6', 'max:30'],
            'accountName' => ['required', 'string', 'min:2', 'max:100'],
        ];
    }

    public function mount(): void
    {
        $this->primaryId = (int) (DB::table('users')
            ->where('id', auth()->id())
            ->value('primary_bank_account_id') ?? 0) ?: null;
    }

    public function add(BankAccountService $banks): void
    {
        $this->validate();

        $banks->createForUser(auth()->id(), [
            'bank_name' => $this->bankName,
            'account_no' => $this->accountNo,
            'account_name' => $this->accountName,
        ]);

        session()->flash('success', 'Rekening berhasil ditambahkan (menunggu verifikasi).');
        $this->reset(['bankName', 'accountNo', 'accountName']);
    }

    public function setPrimary(int $bankAccountId, BankAccountService $banks): void
    {
        $banks->setPrimaryForUser(auth()->id(), $bankAccountId);
        $this->primaryId = $bankAccountId;

        session()->flash('success', 'Rekening utama berhasil diperbarui.');
    }

    public function render()
    {
        $items = DB::table('bank_accounts')
            ->where('owner_type', 'USER')
            ->where('owner_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('livewire.visitor.bank-accounts', compact('items'))
            ->layout('layouts.app', ['title' => 'Rekening Bank']);
    }
}
