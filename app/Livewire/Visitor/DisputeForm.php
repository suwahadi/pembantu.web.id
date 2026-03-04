<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domain\Dispute\Services\DisputeService;
use App\Models\DisputeEvidence;

class DisputeForm extends Component
{
    use WithFileUploads;

    public int $orderId;
    public bool $embedded = false;
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

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function submit(DisputeService $disputes): void
    {
        $this->validate();

        try {
            $dispute = $disputes->openDispute(
                orderId: $this->orderId,
                complaint: '[' . $this->category . '] ' . $this->description
            );

            if ($this->evidence) {
                $path = $this->evidence->store('disputes', 'public');

                DisputeEvidence::create([
                    'dispute_id' => $dispute->id,
                    'submitted_by_type' => 'visitor',
                    'submitted_by_id' => Auth::id(),
                    'file_path' => $path,
                    'description' => 'Bukti awal dari visitor saat pengajuan dispute.',
                ]);
            }

            session()->flash('success', 'Dispute berhasil diajukan. Tim kami akan meninjau dalam 24 jam.');
            $this->redirectRoute('orders.show', $this->orderId);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $view = view('livewire.visitor.dispute-form', [
            'categories' => [
                'tidak_hadir' => 'Tenaga kerja tidak hadir',
                'tidak_sesuai' => 'Tidak sesuai deskripsi',
                'pelayanan_buruk' => 'Pelayanan buruk',
                'lainnya' => 'Lainnya',
            ],
        ]);

        if ($this->embedded) {
            return $view;
        }

        return $view->layout('layouts.app', ['title' => 'Ajukan Dispute']);
    }
}
