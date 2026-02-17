<?php

namespace App\Domain\Ledger\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * LedgerService - Record semua mutasi uang dengan idempotency
 * 
 * Setiap entry wajib memiliki entry_key UNIQUE untuk mencegah duplikasi
 * Jika duplicate key terjadi, treat sebagai success (idempotent)
 */
class LedgerService
{
    /**
     * Record ledger entry dengan idempotency via entry_key
     * Jika entry sudah ada (duplicate key), return gracefully
     */
    public function record(
        string $entryKey,
        string $debitAccount,
        string $creditAccount,
        int $amountIdr,
        string $refType,
        int $refId,
        ?string $note = null
    ): void {
        if ($amountIdr <= 0) {
            return; // Jangan record nominal 0 atau negatif
        }

        try {
            DB::table('wallet_ledgers')->insert([
                'ref_type' => $refType,
                'ref_id' => $refId,
                'entry_key' => $entryKey,
                'debit_account' => $debitAccount,
                'credit_account' => $creditAccount,
                'amount_idr' => $amountIdr,
                'note' => $note,
                'created_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Handle duplicate entry_key - treat as idempotent success
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'unique')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * Get account balance (kredit - debit)
     */
    public function getAccountBalance(string $account): int
    {
        $credit = (int) DB::table('wallet_ledgers')
            ->where('credit_account', $account)
            ->sum('amount_idr');
        
        $debit = (int) DB::table('wallet_ledgers')
            ->where('debit_account', $account)
            ->sum('amount_idr');
        
        return $credit - $debit;
    }

    /**
     * Get ledger entries untuk reference tertentu
     */
    public function getEntriesByRef(string $refType, int $refId)
    {
        return DB::table('wallet_ledgers')
            ->where('ref_type', $refType)
            ->where('ref_id', $refId)
            ->orderBy('created_at')
            ->get();
    }
}
