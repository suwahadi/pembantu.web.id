<?php

namespace App\Domain\Ledger\Services;

use App\Models\WalletLedger;
use App\Domain\Shared\Support\Idempotency;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    /**
     * Create ledger entry dengan idempotency via entry_key
     * Jika entry sudah ada, treat as success (duplicate call)
     */
    public function createEntry(
        string $entryKey,
        string $debitAccount,
        string $creditAccount,
        int $amountIdr,
        string $refType,
        int $refId,
        string $description = '',
        array $metadata = []
    ): WalletLedger {
        return DB::transaction(function () use (
            $entryKey,
            $debitAccount,
            $creditAccount,
            $amountIdr,
            $refType,
            $refId,
            $description,
            $metadata
        ) {
            // Check if entry already exists
            if ($existing = WalletLedger::findByEntryKey($entryKey)) {
                return $existing;
            }

            return WalletLedger::create([
                'entry_key' => $entryKey,
                'debit_account' => $debitAccount,
                'credit_account' => $creditAccount,
                'amount_idr' => $amountIdr,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Get account balance
     */
    public function getAccountBalance(string $account): int
    {
        $credit = WalletLedger::where('credit_account', $account)->sum('amount_idr');
        $debit = WalletLedger::where('debit_account', $account)->sum('amount_idr');
        return $credit - $debit;
    }

    /**
     * Get ledger entries untuk reference tertentu
     */
    public function getEntriesByRef(string $refType, int $refId)
    {
        return WalletLedger::where('ref_type', $refType)
            ->where('ref_id', $refId)
            ->get();
    }
}
