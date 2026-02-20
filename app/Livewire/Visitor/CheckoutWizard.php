<?php

namespace App\Livewire\Visitor;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Contract\Services\ContractService;
use App\Domain\Order\Services\OrderService;
use App\Domain\Payment\Services\MidtransCoreService;
use App\Models\Location;
use App\Models\Worker;

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
        if (!Auth::check()) {
            session()->put('url.intended', url()->current());
            $this->redirectRoute('login', navigate: true);
            return;
        }

        $workerModel = Worker::find($worker);
        if (!$workerModel) {
            abort(404, 'Worker tidak ditemukan.');
        }

        // Agency owner tidak boleh checkout worker milik sendiri
        if ($workerModel->agency && $workerModel->agency->user_id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat memesan tenaga kerja dari agency sendiri.');
            $this->redirectRoute('home', navigate: true);
            return;
        }

        $this->workerId = $worker;
        $this->startDate = now()->addDay()->toDateString();
        $this->endDate = now()->addMonth()->toDateString();
    }

    protected function getRulesForStep(int $step): array
    {
        if ($step === 1) {
            return [
                'scheme' => ['required', 'in:HARIAN,MINGGUAN,BULANAN,PER_JAM'],
                'startDate' => ['required', 'date', 'after_or_equal:today'],
                'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            ];
        }

        if ($step === 2) {
            return [
                'locationId' => ['required', 'integer', 'exists:locations,id'],
                'workAddress' => ['required', 'string', 'min:10', 'max:1000'],
                'scopeOfWork' => ['required', 'string', 'min:10', 'max:2000'],
            ];
        }

        return [];
    }

    public function next(ContractService $contracts): void
    {
        if ($this->step <= 2) {
            $this->validate($this->getRulesForStep($this->step));
        }

        if ($this->step === 2 && !$this->contractId) {
            $draft = $contracts->create($this->workerId, [
                'scheme' => $this->scheme,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'location_id' => $this->locationId,
                'work_address' => $this->workAddress,
                'scope_of_work' => $this->scopeOfWork,
            ]);

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
        $visitorId = (int)Auth::id();
        
        if (!$this->contractId) {
            $this->addError('contractId', 'Kontrak belum dibuat.');
            return;
        }

        $contracts->visitorSign($this->contractId);

        $worker = \App\Models\Worker::find($this->workerId);
        if (!$worker) {
            $this->addError('workerId', 'Worker tidak ditemukan.');
            return;
        }

        if ($worker->min_price <= 0) {
            $this->addError('workerId', 'Worker tidak memiliki harga yang valid. Silakan hubungi agency.');
            return;
        }

        $order = $orders->createFromContract(
            contractId: $this->contractId,
            visitorUserId: $visitorId,
            agencyId: (int)$worker->agency_id,
            workerId: (int)$worker->id,
            categoryId: (int)$worker->category_id,
            totalIdr: (int)$worker->min_price
        );

        $this->orderId = $order->id;
        $this->redirect('/pembayaran/' . $this->orderId);
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
