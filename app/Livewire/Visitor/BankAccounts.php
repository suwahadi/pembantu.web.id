<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domain\Bank\Services\BankAccountService;

final class BankAccounts extends Component
{
    public string $bankCode = '';
    public string $bankName = '';
    public string $accountNo = '';
    public string $accountName = '';
    public ?int $primaryId = null;

    protected function rules(): array
    {
        return [
            'bankCode' => ['required', 'string', 'max:10'],
            'bankName' => ['required', 'string', 'min:2', 'max:100'],
            'accountNo' => ['required', 'string', 'min:8', 'max:30', 'regex:/^[0-9]+$/'],
            'accountName' => ['required', 'string', 'min:3', 'max:120'],
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
        try {
            $validated = $this->validate();

            $banks->createForUser(
                auth()->id(), 
                $validated['bankCode'],
                $validated['bankName'],
                $validated['accountNo'],
                $validated['accountName']
            );

            session()->flash('success', 'Rekening berhasil ditambahkan (menunggu verifikasi).');
            $this->reset(['bankCode', 'bankName', 'accountNo', 'accountName']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function setPrimary(int $bankAccountId, BankAccountService $banks): void
    {
        $banks->setPrimary($bankAccountId);
        $this->primaryId = $bankAccountId;

        session()->flash('success', 'Rekening utama berhasil diperbarui.');
    }

    public function render()
    {
        $items = DB::table('bank_accounts')
            ->where('owner_type', 'App\Models\User')
            ->where('owner_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('livewire.visitor.bank-accounts', compact('items'))
            ->layout('layouts.app', ['title' => 'Rekening Bank']);
    }
}
