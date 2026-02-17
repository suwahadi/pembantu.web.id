<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domain\Dispute\Services\DisputeService;

class DisputeForm extends Component
{
    use WithFileUploads;

    public int $orderId;
    public string $category = 'tidak_sesuai';
    public string $description = '';
    public $evidence = null;

    protected function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,mp4'],
        ];
    }

    public function mount(int $order): void
    {
        $this->orderId = $order;
    }

    public function submit(DisputeService $disputes): void
    {
        $this->validate();

        $userId = Auth::id();
        $disputeId = $disputes->openDispute(
            orderId: $this->orderId,
            userId: $userId,
            reason: $this->description,
            category: $this->category
        );

        if ($this->evidence) {
            $path = $this->evidence->store('disputes', 'public');
            // Additional evidence handling if needed
        }

        session()->flash('success', 'Dispute berhasil diajukan. Tim kami akan meninjau dalam 24 jam.');
        $this->redirectRoute('orders.show', $this->orderId);
    }

    public function render()
    {
        return view('livewire.visitor.dispute-form', [
            'categories' => [
                'tidak_hadir' => 'Tenaga kerja tidak hadir',
                'tidak_sesuai' => 'Tidak sesuai deskripsi',
                'pelayanan_buruk' => 'Pelayanan buruk',
                'lainnya' => 'Lainnya',
            ],
        ])->layout('layouts.app', ['title' => 'Ajukan Dispute']);
    }
}
