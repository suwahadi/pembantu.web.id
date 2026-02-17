<?php

namespace App\Domain\Contract\Services;

use App\Models\{Contract, Order};
use App\Domain\Order\Services\OrderService;
use App\Domain\Audit\Services\AuditLogService;
use Illuminate\Support\Facades\{DB, Auth};

class ContractService
{
    public function __construct(private OrderService $orderService) {}

    /**
     * Create contract dari specification
     */
    public function create(
        int $workerId,
        string $jobScope,
        string $locationAddress,
        string $startDate,
        ?string $endDate = null,
        ?string $termsConditions = null,
        array $metadata = []
    ): Contract {
        return DB::transaction(function () use (
            $workerId,
            $jobScope,
            $locationAddress,
            $startDate,
            $endDate,
            $termsConditions,
            $metadata
        ) {
            $contractNo = 'CTR-' . now()->format('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $contract = Contract::create([
                'contract_no' => $contractNo,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'job_scope' => $jobScope,
                'location_address' => $locationAddress,
                'terms_conditions' => $termsConditions,
                'metadata' => $metadata,
            ]);

            AuditLogService::record('contract_created', 'CONTRACT', $contract->id);

            return $contract;
        });
    }

    /**
     * Visitor sign contract
     */
    public function visitorSign(int $contractId): Contract
    {
        return DB::transaction(function () use ($contractId) {
            $contract = Contract::lockForUpdate()->findOrFail($contractId);

            if ($contract->visitor_signed) {
                return $contract;
            }

            $contract->update([
                'visitor_signed' => true,
                'visitor_signed_at' => now(),
            ]);

            AuditLogService::record('contract_visitor_signed', 'CONTRACT', $contract->id);

            return $contract;
        });
    }

    /**
     * Agency sign contract
     */
    public function agencySign(int $contractId): Contract
    {
        return DB::transaction(function () use ($contractId) {
            $contract = Contract::lockForUpdate()->findOrFail($contractId);

            if ($contract->agency_signed) {
                return $contract;
            }

            $contract->update([
                'agency_signed' => true,
                'agency_signed_at' => now(),
            ]);

            AuditLogService::record('contract_agency_signed', 'CONTRACT', $contract->id);

            return $contract;
        });
    }

    /**
     * Get contract
     */
    public function get(int $contractId): ?Contract
    {
        return Contract::find($contractId);
    }

    /**
     * Check apakah contract sudah fully signed
     */
    public function isFullySigned(int $contractId): bool
    {
        $contract = $this->get($contractId);
        return $contract && $contract->bothSigned();
    }
}
