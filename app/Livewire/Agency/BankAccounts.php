<?php

namespace App\Livewire\Agency;

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
        $agencyId = auth()->user()->agency_id;
        $this->primaryId = (int) (DB::table('agencies')
            ->where('id', $agencyId)
            ->value('primary_bank_account_id') ?? 0) ?: null;
    }

    public function add(BankAccountService $banks): void
    {
        try {
            $validated = $this->validate();
            
            $agencyId = auth()->user()->agency_id;
            $banks->createForAgency(
                $agencyId, 
                $validated['bankCode'], 
                $validated['bankName'], 
                $validated['accountNo'], 
                $validated['accountName']
            );

            session()->flash('success', 'Rekening berhasil ditambahkan (menunggu verifikasi).');
            $this->reset(['bankCode', 'bankName', 'accountNo', 'accountName']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation exception handled by Livewire automatically
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function setPrimary(int $bankAccountId, BankAccountService $banks): void
    {
        $agencyId = auth()->user()->agency_id;
        $banks->setPrimaryForAgency($agencyId, $bankAccountId);
        $this->primaryId = $bankAccountId;

        session()->flash('success', 'Rekening utama berhasil diperbarui.');
    }

    public function render()
    {
        $agencyId = auth()->user()->agency_id;
        $items = DB::table('bank_accounts')
            ->where('owner_type', 'AGENCY')
            ->where('owner_id', $agencyId)
            ->orderByDesc('id')
            ->get();

        return view('livewire.agency.bank-accounts', compact('items'))
            ->layout('layouts.agency', ['title' => 'Rekening Bank']);
    }
}
