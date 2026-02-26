<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

final class BankAccounts extends Component
{
    use WithPagination;

    public string $ownerType = '';
    public string $verifiedStatus = '';
    public string $q = '';

    public ?int $selectedId = null;
    public string $setStatus = 'verified';
    public string $adminNote = '';

    protected $queryString = ['ownerType', 'verifiedStatus', 'q'];

    public function select(int $id): void
    {
        $this->selectedId = $id;
        $this->adminNote = '';
        $this->setStatus = 'verified';
    }

    public function verify(): void
    {
        if (!$this->selectedId) {
            return;
        }

        DB::transaction(function () {
            $row = DB::table('bank_accounts')
                ->where('id', $this->selectedId)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                return;
            }

            DB::table('bank_accounts')
                ->where('id', $row->id)
                ->update([
                    'verified_status' => $this->setStatus,
                    'verified_at' => $this->setStatus === 'verified' ? now() : null,
                    'admin_note' => $this->adminNote ?: null,
                    'updated_at' => now(),
                ]);
        });

        session()->flash('success', 'Status verifikasi rekening diperbarui.');
        $this->selectedId = null;
        $this->adminNote = '';
        $this->setStatus = 'verified';
    }

    public function render()
    {
        $query = DB::table('bank_accounts')
            ->select(['id', 'owner_type', 'owner_id', 'bank_name', 'account_no', 'account_name', 'verified_status', 'verified_at', 'created_at'])
            ->orderByDesc('created_at');

        if ($this->ownerType !== '') {
            $query->where('owner_type', 'ilike', '%' . $this->ownerType . '%');
        }

        if ($this->verifiedStatus !== '') {
            $query->where('verified_status', $this->verifiedStatus);
        }

        if ($this->q !== '') {
            $q = '%' . $this->q . '%';
            $query->where(function ($w) use ($q) {
                $w->where('bank_name', 'like', $q)
                    ->orWhere('account_no', 'like', $q)
                    ->orWhere('account_name', 'like', $q);
            });
        }

        $items = $query->paginate(20);

        $selected = null;
        if ($this->selectedId) {
            $selected = DB::table('bank_accounts')->where('id', $this->selectedId)->first();
        }

        return view('livewire.admin.bank-accounts', compact('items', 'selected'))
            ->layout('layouts.admin', ['title' => 'Rekening Bank']);
    }
}
