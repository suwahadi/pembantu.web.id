<?php

namespace App\Domain\Bank\Services;

use App\Models\{User, Agency, BankAccount};
use App\Domain\Audit\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class BankAccountService
{
    /**
     * Create bank account untuk user
     */
    public function createForUser(int $userId, string $bankCode, string $bankName, string $accountNo, string $accountName): BankAccount
    {
        return DB::transaction(function () use ($userId, $bankCode, $bankName, $accountNo, $accountName) {
            $bankAccount = BankAccount::create([
                'owner_type' => 'App\Models\User',
                'owner_id' => $userId,
                'bank_code' => $bankCode,
                'bank_name' => $bankName,
                'account_no' => $accountNo,
                'account_name' => $accountName,
                'verified_status' => 'unverified',
            ]);

            AuditLogService::record('bank_account_created', 'BANK_ACCOUNT', $bankAccount->id);

            return $bankAccount;
        });
    }

    /**
     * Create bank account untuk agency
     */
    public function createForAgency(int $agencyId, string $bankCode, string $bankName, string $accountNo, string $accountName): BankAccount
    {
        return DB::transaction(function () use ($agencyId, $bankCode, $bankName, $accountNo, $accountName) {
            $bankAccount = BankAccount::create([
                'owner_type' => 'App\Models\Agency',
                'owner_id' => $agencyId,
                'bank_code' => $bankCode,
                'bank_name' => $bankName,
                'account_no' => $accountNo,
                'account_name' => $accountName,
                'verified_status' => 'unverified',
            ]);

            AuditLogService::record('bank_account_created', 'BANK_ACCOUNT', $bankAccount->id);

            return $bankAccount;
        });
    }

    /**
     * Verify bank account (manual oleh admin)
     */
    public function verify(int $bankAccountId): BankAccount
    {
        return DB::transaction(function () use ($bankAccountId) {
            $account = BankAccount::lockForUpdate()->findOrFail($bankAccountId);

            $account->update([
                'verified_status' => 'verified',
                'verified_at' => now(),
            ]);

            AuditLogService::record('bank_account_verified', 'BANK_ACCOUNT', $account->id);

            return $account;
        });
    }

    /**
     * Reject bank account
     */
    public function reject(int $bankAccountId, string $reason = ''): BankAccount
    {
        return DB::transaction(function () use ($bankAccountId, $reason) {
            $account = BankAccount::lockForUpdate()->findOrFail($bankAccountId);

            $account->update([
                'verified_status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            AuditLogService::record('bank_account_rejected', 'BANK_ACCOUNT', $account->id);

            return $account;
        });
    }

    /**
     * Set sebagai primary
     */
    public function setPrimary(int $bankAccountId): BankAccount
    {
        return DB::transaction(function () use ($bankAccountId) {
            $account = BankAccount::findOrFail($bankAccountId);

            // Remove primary flag dari yang lain (owner type + owner id yang sama)
            BankAccount::where('owner_type', $account->owner_type)
                ->where('owner_id', $account->owner_id)
                ->where('id', '!=', $bankAccountId)
                ->update(['is_primary' => false]);

            $account->update(['is_primary' => true]);

            // Update user atau agency primary_bank_account_id
            if ($account->owner_type === 'App\Models\User') {
                User::find($account->owner_id)->update(['primary_bank_account_id' => $account->id]);
            } elseif ($account->owner_type === 'App\Models\Agency') {
                Agency::find($account->owner_id)->update(['primary_bank_account_id' => $account->id]);
            }

            AuditLogService::record('bank_account_set_primary', 'BANK_ACCOUNT', $account->id);

            return $account;
        });
    }

    /**
     * Get verified bank accounts untuk user
     */
    public function getVerifiedForUser(int $userId)
    {
        return BankAccount::where('owner_type', 'App\Models\User')
            ->where('owner_id', $userId)
            ->where('verified_status', 'verified')
            ->get();
    }

    /**
     * Get verified bank accounts untuk agency
     */
    public function getVerifiedForAgency(int $agencyId)
    {
        return BankAccount::where('owner_type', 'App\Models\Agency')
            ->where('owner_id', $agencyId)
            ->where('verified_status', 'verified')
            ->get();
    }

    /**
     * Delete bank account
     */
    public function delete(int $bankAccountId): bool
    {
        return DB::transaction(function () use ($bankAccountId) {
            $account = BankAccount::find($bankAccountId);
            if ($account) {
                AuditLogService::record('bank_account_deleted', 'BANK_ACCOUNT', $account->id);
                return (bool) $account->delete();
            }
            return false;
        });
    }
}
