<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\PaymentAttempt;
use Livewire\Component;

class PaymentDetail extends Component
{
    public int $paymentId;

    public array $payment = [];
    public array $attempts = [];

    public function mount(int $paymentId): void
    {
        $this->paymentId = $paymentId;
        $this->loadPayment();
    }

    public function render()
    {
        return view('livewire.admin.payment-detail')
            ->layout('layouts.admin', ['title' => 'Detail Payment']);
    }

    public function loadPayment(): void
    {
        $payment = Payment::with(['order', 'order.visitor', 'order.agency'])->find($this->paymentId);

        if (!$payment) {
            $this->payment = [];
            $this->attempts = [];
            return;
        }

        $this->payment = [
            'id' => $payment->id,
            'order_id' => $payment->order_id,
            'midtrans_order_id' => $payment->midtrans_order_id,
            'transaction_id' => $payment->transaction_id,
            'status' => $payment->status,
            'amount_idr' => (int) $payment->amount_idr,
            'payment_method' => $payment->payment_method,
            'settled_at' => optional($payment->settled_at)->format('d M Y H:i'),
            'created_at' => optional($payment->created_at)->format('d M Y H:i'),
            'updated_at' => optional($payment->updated_at)->format('d M Y H:i'),
            'request_payload' => $payment->request_payload ?? [],
            'last_callback_payload' => $payment->last_callback_payload ?? [],
            'order' => $payment->order ? [
                'code' => $payment->order->code,
                'status' => $payment->order->status,
                'total_idr' => (int) ($payment->order->total_idr ?? 0),
                'visitor_name' => optional($payment->order->visitor)->name,
                'visitor_email' => optional($payment->order->visitor)->email,
                'agency_name' => optional($payment->order->agency)->company_name ?? optional($payment->order->agency)->name,
            ] : null,
        ];

        $attempts = PaymentAttempt::where('order_id', $payment->order_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->attempts = $attempts->map(function ($a) {
            return [
                'id' => $a->id,
                'midtrans_order_id' => $a->midtrans_order_id,
                'transaction_id' => $a->transaction_id,
                'status' => $a->status,
                'amount_idr' => (int) $a->amount_idr,
                'callback_received_at' => optional($a->callback_received_at)->format('d M Y H:i'),
                'processed_at' => optional($a->processed_at)->format('d M Y H:i'),
                'error_message' => $a->error_message,
                'raw_payload' => $a->raw_payload ?? [],
                'created_at' => optional($a->created_at)->format('d M Y H:i'),
            ];
        })->toArray();
    }
}
