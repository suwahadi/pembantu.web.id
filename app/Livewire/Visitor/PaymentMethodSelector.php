<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Payment\Services\MidtransCoreService;
use App\Models\Order;

class PaymentMethodSelector extends Component
{
    public int $orderId;
    public ?Order $order = null;
    public ?string $selectedPaymentType = null;
    public ?string $selectedBank = null;
    public ?array $paymentDetails = null;
    public bool $processing = false;

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
        } catch (\Throwable $e) {
            $this->addError('payment', $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function render()
    {
        return view('livewire.visitor.payment-method-selector', [
            'banks' => [
                'bca' => 'BCA',
                'bni' => 'BNI',
                'mandiri' => 'Mandiri',
                'bri' => 'BRI',
                'permata' => 'Permata',
            ],
        ])->layout('layouts.app', ['title' => 'Pilih Metode Pembayaran']);
    }
}
