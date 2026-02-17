<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\{
    Payment\Services\PaymentService,
    Ledger\Services\LedgerService,
    Escrow\Services\EscrowService,
    Event\Services\OrderEventService,
    Audit\Services\AuditLogService,
    Order\Services\OrderService,
    Dispute\Services\DisputeService,
    Refund\Services\RefundService,
    Payout\Services\PayoutService,
    Contract\Services\ContractService,
    Bank\Services\BankAccountService,
    Worker\Services\WorkerCatalogService,
};

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register service layer sehingga dapat di-inject ke Livewire dan Controller
        $this->app->singleton(LedgerService::class, function ($app) {
            return new LedgerService();
        });

        $this->app->singleton(EscrowService::class, function ($app) {
            return new EscrowService($app->make(LedgerService::class));
        });

        $this->app->singleton(OrderEventService::class, function ($app) {
            return new OrderEventService();
        });

        $this->app->singleton(AuditLogService::class, function ($app) {
            return new AuditLogService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(LedgerService::class),
                $app->make(EscrowService::class),
                $app->make(OrderEventService::class),
                $app->make(AuditLogService::class),
            );
        });

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService($app->make(OrderEventService::class));
        });

        $this->app->singleton(DisputeService::class, function ($app) {
            return new DisputeService(
                $app->make(EscrowService::class),
                $app->make(OrderEventService::class),
            );
        });

        $this->app->singleton(RefundService::class, function ($app) {
            return new RefundService(
                $app->make(LedgerService::class),
                $app->make(OrderEventService::class),
            );
        });

        $this->app->singleton(PayoutService::class, function ($app) {
            return new PayoutService(
                $app->make(LedgerService::class),
                $app->make(OrderEventService::class),
            );
        });

        $this->app->singleton(ContractService::class, function ($app) {
            return new ContractService($app->make(OrderService::class));
        });

        $this->app->singleton(BankAccountService::class, function ($app) {
            return new BankAccountService();
        });

        $this->app->singleton(WorkerCatalogService::class, function ($app) {
            return new WorkerCatalogService();
        });
    }

    public function boot(): void
    {
        // Boot logic jika diperlukan
    }
}
