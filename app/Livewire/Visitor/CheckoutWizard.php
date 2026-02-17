<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Contract\Services\ContractService;
use App\Domain\Order\Services\OrderService;
use App\Domain\Payment\Services\MidtransCoreService;
use App\Models\Location;

class CheckoutWizard extends Component
{
    public int $workerId;
    public int $step = 1;

    public string $scheme = 'BULANAN';
    public string $startDate = '';
    public string $endDate = '';
    public ?int $locationId = null;
    public string $workAddress = '';
    public string $scopeOfWork = '';

    public ?int $contractId = null;
    public ?int $orderId = null;
    public ?array $paymentInstruction = null;

    public function mount(int $worker): void
    {
        $this->workerId = $worker;
        $this->startDate = now()->addDay()->toDateString();
        $this->endDate = now()->addMonth()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'scheme' => ['required', 'in:HARIAN,MINGGUAN,BULANAN,PER_JAM'],
            'startDate' => ['required', 'date', 'after_or_equal:today'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'locationId' => ['required', 'integer', 'exists:locations,id'],
            'workAddress' => ['required', 'string', 'min:10', 'max:1000'],
            'scopeOfWork' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function next(ContractService $contracts): void
    {
        if ($this->step <= 2) {
            $this->validate();
        }

        if ($this->step === 2 && !$this->contractId) {
            $visitorId = Auth::id();

            $draft = $contracts->create(
                visitorId: $visitorId,
                workerId: $this->workerId,
                data: [
                    'scheme' => $this->scheme,
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate,
                    'location_id' => $this->locationId,
                    'work_address' => $this->workAddress,
                    'scope_of_work' => $this->scopeOfWork,
                ]
            );

            $this->contractId = $draft->id;
        }

        $this->step++;
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function signAndPay(ContractService $contracts, OrderService $orders, MidtransCoreService $midtrans): void
    {
        $visitorId = Auth::id();
        
        if (!$this->contractId) {
            $this->addError('contractId', 'Kontrak belum dibuat.');
            return;
        }

        $contracts->signByVisitor($this->contractId, $visitorId);

        $order = $orders->createFromContract($this->contractId, $visitorId);
        $this->orderId = $order->id;

        $this->paymentInstruction = $midtrans->charge($order->id);

        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.visitor.checkout-wizard', [
            'locations' => Location::distinct()->select('id', 'city')->orderBy('city')->get()->mapWithKeys(fn($loc) => [$loc->id => $loc->city])->toArray(),
            'schemes' => [
                'HARIAN' => 'Harian',
                'MINGGUAN' => 'Mingguan',
                'BULANAN' => 'Bulanan',
                'PER_JAM' => 'Per Jam',
            ],
        ])->layout('layouts.app', ['title' => 'Checkout']);
    }
}
