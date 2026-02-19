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
    public ?int $agencyId = null;

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
        $agency = auth()->user()->agency;
        if ($agency) {
            $this->agencyId = $agency->id;
            $this->primaryId = $agency->primary_bank_account_id;
        }
    }

    public function add(BankAccountService $banks): void
    {
        if (!$this->agencyId) {
            session()->flash('error', 'Data agensi tidak ditemukan.');
            return;
        }

        try {
            $validated = $this->validate();
            
            $banks->createForAgency(
                $this->agencyId, 
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
            $this->dispatch('error', message: 'Error: ' . $e->getMessage());
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
        $items = $this->agencyId 
            ? DB::table('bank_accounts')
                ->where('owner_type', 'App\Models\Agency')
                ->where('owner_id', $this->agencyId)
                ->orderByDesc('id')
                ->get()
            : collect([]);

        return view('livewire.agency.bank-accounts', compact('items'))
            ->layout('layouts.agency', ['title' => 'Rekening Bank']);
    }
}
