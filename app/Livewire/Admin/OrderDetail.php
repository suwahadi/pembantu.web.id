<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Domain\Shared\Statuses\OrderStatus;
use Illuminate\Support\Facades\Log;

final class OrderDetail extends Component
{
    public int $orderId;
    public array $order = [];
    public array $events = [];
    public array $statusLabels = [];

    /**
     * @var array<string, mixed>
     */
    public array $form = [
        'status' => '',
        'start_date' => null,
        'end_date' => null,
        'subtotal_idr' => 0,
        'platform_fee_idr' => 0,
        'total_idr' => 0,
        'currency' => 'IDR',
        'notes' => '',
    ];

    public bool $showDeleteConfirm = false;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
        $this->statusLabels = OrderStatus::labels();
        $this->loadOrder();
    }

    public function render()
    {
        return view('livewire.admin.order-detail')
            ->layout('layouts.admin', ['title' => 'Detail Order']);
    }

    public function loadOrder(): void
    {
        $order = $this->resolveOrder(withRelations: true);

        $this->order = [
            'id' => $order->id,
            'code' => $order->code,
            'status' => $order->status,
            'status_label' => OrderStatus::label($order->status),
            'duration' => $order->duration ?? 0,
            'start_date' => optional($order->start_date)->format('Y-m-d'),
            'end_date' => optional($order->end_date)->format('Y-m-d'),
            'start_date_human' => optional($order->start_date)->translatedFormat('d F Y'),
            'end_date_human' => optional($order->end_date)->translatedFormat('d F Y'),
            'subtotal_idr' => $order->subtotal_idr,
            'platform_fee_idr' => $order->platform_fee_idr,
            'total_idr' => $order->total_idr,
            'currency' => $order->currency ?? 'IDR',
            'notes' => $order->notes,
            'completed_at' => optional($order->completed_at)->format('d M Y H:i'),
            'cancelled_at' => optional($order->cancelled_at)->format('d M Y H:i'),
            'created_at' => optional($order->created_at)->format('d M Y H:i'),
            'updated_at' => optional($order->updated_at)->format('d M Y H:i'),
            'visitor' => $order->visitor ? [
                'id' => $order->visitor->id,
                'name' => $order->visitor->name,
                'email' => $order->visitor->email,
                'phone' => $order->visitor->phone ?? '-',
            ] : null,
            'agency' => $order->agency ? [
                'id' => $order->agency->id,
                'company_name' => $order->agency->company_name,
                'email' => optional($order->agency->user)->email,
                'phone' => $order->agency->phone ?? optional($order->agency->user)->phone ?? '-',
            ] : null,
            'worker' => $order->worker ? [
                'id' => $order->worker->id,
                'name' => $order->worker->name,
                'phone' => $order->worker->phone ?? '-',
            ] : null,
            'category' => $order->category ? [
                'id' => $order->category->id,
                'name' => $order->category->name,
            ] : null,
            'contract' => $order->contract ? [
                'start_date' => optional($order->contract->start_date)->format('d M Y'),
                'end_date' => optional($order->contract->end_date)->format('d M Y'),
                'metadata' => $this->normalizeMetadata($order->contract->metadata),
            ] : null,
            'events' => $order->events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'description' => $event->description,
                    'created_at' => optional($event->created_at)->format('d M Y H:i'),
                    'status_from' => $event->status_from,
                    'status_to' => $event->status_to,
                    'actor_type' => $event->actor_type,
                ];
            })->toArray(),
        ];

        $this->events = $order->events->map(function ($event) {
            return [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'description' => $event->description,
                'created_at' => optional($event->created_at)->format('d M Y H:i'),
                'status_from' => $event->status_from,
                'status_to' => $event->status_to,
                'actor_type' => $event->actor_type,
            ];
        })->toArray();

        $this->form = [
            'status' => $order->status,
            'start_date' => optional($order->start_date)->format('Y-m-d'),
            'end_date' => optional($order->end_date)->format('Y-m-d'),
            'subtotal_idr' => $order->subtotal_idr,
            'platform_fee_idr' => $order->platform_fee_idr,
            'total_idr' => $order->total_idr,
            'currency' => $order->currency ?? 'IDR',
            'notes' => $order->notes,
        ];
    }

    public function updateOrder(): void
    {
        $data = $this->validate($this->rules(), $this->messages());

        $payload = $data['form'];
        $payload['currency'] = strtoupper($payload['currency']);
        $payload['start_date'] = $payload['start_date'] ?: null;
        $payload['end_date'] = $payload['end_date'] ?: null;
        $payload['notes'] = $payload['notes'] ?: null;

        try {
            $order = $this->fetchOrderModel();
            $order->update($payload);
            session()->flash('success', 'Order berhasil diperbarui.');
            $this->loadOrder();
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui order', ['error' => $e->getMessage()]);
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function confirmDelete(): void
    {
        $this->dispatch('show-delete-confirm');
    }

    public function deleteOrder(): void
    {
        try {
            $order = $this->fetchOrderModel();
            $order->delete();
            session()->flash('success', 'Order berhasil dihapus.');
            $this->redirectRoute('admin.orders.index');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus order', ['error' => $e->getMessage()]);
            session()->flash('error', 'Tidak dapat menghapus order saat ini.');
        }
    }

    protected function rules(): array
    {
        return [
            'form.status' => 'required|in:' . implode(',', OrderStatus::all()),
            'form.start_date' => 'nullable|date',
            'form.end_date' => 'nullable|date|after_or_equal:form.start_date',
            'form.subtotal_idr' => 'required|numeric|min:0',
            'form.platform_fee_idr' => 'required|numeric|min:0',
            'form.total_idr' => 'required|numeric|min:0',
            'form.currency' => 'required|string|size:3',
            'form.notes' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.status.required' => 'Status wajib dipilih.',
            'form.status.in' => 'Status tidak valid.',
            'form.start_date.date' => 'Tanggal mulai tidak valid.',
            'form.end_date.date' => 'Tanggal selesai tidak valid.',
            'form.end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
            'form.subtotal_idr.required' => 'Subtotal wajib diisi.',
            'form.subtotal_idr.numeric' => 'Subtotal harus berupa angka.',
            'form.platform_fee_idr.required' => 'Platform fee wajib diisi.',
            'form.total_idr.required' => 'Total wajib diisi.',
            'form.currency.size' => 'Mata uang harus 3 huruf (misal: IDR).',
            'form.notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    private function resolveOrder(bool $withRelations = false): Order
    {
        $query = Order::withTrashed();

        if ($withRelations) {
            $query->with([
                'visitor:id,name,email,phone',
                'agency:id,user_id,company_name,phone',
                'agency.user:id,name,email,phone',
                'worker:id,name,phone',
                'category:id,name',
                'contract:id,order_id,start_date,end_date,metadata',
                'events' => function ($builder) {
                    $builder->latest()->limit(20);
                },
            ]);
        }

        return $query->findOrFail($this->orderId);
    }

    private function fetchOrderModel(): Order
    {
        return Order::withTrashed()->findOrFail($this->orderId);
    }

    /**
     * @param  mixed  $metadata
     */
    private function normalizeMetadata($metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (is_string($metadata) && $metadata !== '') {
            $decoded = json_decode($metadata, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
