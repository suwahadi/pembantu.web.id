<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Payment\Services\MidtransCoreService;
use App\Domain\Shared\Statuses\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;

class PaymentMethodSelector extends Component
{
    public int $orderId;
    public ?Order $order = null;
    public ?string $selectedPaymentType = null;
    public ?string $selectedBank = null;
    public ?array $paymentDetails = null;
    public bool $processing = false;

    private function encodePaymentMethod(string $type, ?string $bank): string
    {
        if ($type === 'bank_transfer') {
            return 'bank_transfer:' . ($bank ?? '');
        }

        return $type;
    }

    private function applyPaymentMethodFromString(?string $method): void
    {
        if (!$method) {
            return;
        }

        if (str_starts_with($method, 'bank_transfer:')) {
            $this->selectedPaymentType = 'bank_transfer';
            $bank = trim(substr($method, strlen('bank_transfer:')));
            $this->selectedBank = $bank !== '' ? $bank : null;
            return;
        }

        $this->selectedPaymentType = $method;
        $this->selectedBank = null;
    }

    private function loadExistingPayment(): void
    {
        if ($this->order && $this->order->status === 'paid_escrow') {
            $this->paymentDetails = [
                'transaction_status' => 'settlement',
                'settled_at' => now()->toDateTimeString(),
                'payment_success' => true
            ];
            return;
        }

        $existingPayment = Payment::where('order_id', $this->orderId)->first();
        if (
            $existingPayment
            && !PaymentStatus::isFailure((string)$existingPayment->status)
            && !empty($existingPayment->request_payload)
        ) {
            $this->paymentDetails = (array) $existingPayment->request_payload;
            $this->applyPaymentMethodFromString($existingPayment->payment_method);
        }
    }

    public function mount(string $orderId): void
    {
        if (!Auth::check()) {
            session()->put('url.intended', url()->current());
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $this->orderId = (int)$orderId;
        $this->order = Order::with(['visitor', 'worker'])->find($this->orderId);

        if (!$this->order || $this->order->visitor_user_id !== Auth::id()) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        $this->loadExistingPayment();
    }

    public function refreshPayment(): void
    {
        $this->resetErrorBag('payment');
        $this->loadExistingPayment();

        if ($this->paymentDetails) {
            $this->dispatch('toast', message: 'Pembayaran diperbarui.');
            return;
        }

        $this->dispatch('toast', message: 'Data pembayaran belum tersedia.');
    }

    public function selectBankTransfer(string $bank): void
    {
        $this->selectedPaymentType = 'bank_transfer';
        $this->selectedBank = $bank;
    }

    public function selectGopay(): void
    {
        $this->selectedPaymentType = 'gopay';
        $this->selectedBank = null;
    }

    public function processPayment(MidtransCoreService $midtrans): void
    {
        $existingPayment = Payment::where('order_id', $this->orderId)->first();
        if (
            $existingPayment
            && !PaymentStatus::isFailure((string)$existingPayment->status)
            && !empty($existingPayment->request_payload)
        ) {
            $this->paymentDetails = (array) $existingPayment->request_payload;
            $this->applyPaymentMethodFromString($existingPayment->payment_method);
            return;
        }

        if (!$this->selectedPaymentType) {
            $this->addError('payment', 'Pilih metode pembayaran.');
            return;
        }

        $this->processing = true;

        try {
            $this->paymentDetails = $midtrans->charge(
                $this->orderId,
                $this->selectedPaymentType,
                $this->selectedBank
            );

            Payment::updateOrCreate(
                ['order_id' => $this->orderId],
                [
                    'midtrans_order_id' => $this->paymentDetails['order_id'] ?? ($existingPayment?->midtrans_order_id),
                    'transaction_id' => $this->paymentDetails['transaction_id'] ?? null,
                    'status' => $this->paymentDetails['transaction_status'] ?? PaymentStatus::PENDING,
                    'amount_idr' => (int)($this->order->total_idr ?? 0),
                    'payment_method' => $this->encodePaymentMethod($this->selectedPaymentType, $this->selectedBank),
                    'request_payload' => $this->paymentDetails,
                ]
            );
        } catch (\Throwable $e) {
            $this->addError('payment', $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function render()
    {
        $title = $this->paymentDetails ? 'Instruksi Pembayaran' : 'Pilih Metode Pembayaran';

        return view('livewire.visitor.payment-method-selector', [
            'banks' => [
                'bca' => 'BCA',
                'bni' => 'BNI',
                'mandiri' => 'Mandiri',
                'bri' => 'BRI',
                'permata' => 'Permata',
            ],
        ])->layout('layouts.app', ['title' => $title]);
    }
}
