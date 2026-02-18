<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add snapshot columns ke refunds table (archive rekening saat transfer)
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds', 'bank_name_snapshot')) {
                $table->string('bank_name_snapshot', 100)->nullable()->after('bank_account_id');
                $table->string('account_no_snapshot', 50)->nullable()->after('bank_name_snapshot');
                $table->string('account_name_snapshot', 150)->nullable()->after('account_no_snapshot');
                $table->string('transfer_ref', 150)->nullable()->after('paid_at'); // Reference dari bank/sistem
            }
        });

        // Add snapshot columns ke payouts table
        Schema::table('payouts', function (Blueprint $table) {
            if (!Schema::hasColumn('payouts', 'bank_name_snapshot')) {
                $table->string('bank_name_snapshot', 100)->nullable()->after('bank_account_id');
                $table->string('account_no_snapshot', 50)->nullable()->after('bank_name_snapshot');
                $table->string('account_name_snapshot', 150)->nullable()->after('account_no_snapshot');
                $table->string('transfer_ref', 150)->nullable()->after('paid_at'); // Reference dari bank/sistem
            }
        });

        // Add admin audit fields ke refunds/payouts
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds', 'verified_by_admin_user_id')) {
                $table->unsignedBigInteger('verified_by_admin_user_id')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->foreign('verified_by_admin_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        Schema::table('payouts', function (Blueprint $table) {
            if (!Schema::hasColumn('payouts', 'verified_by_admin_user_id')) {
                $table->unsignedBigInteger('verified_by_admin_user_id')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->foreign('verified_by_admin_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['verified_by_admin_user_id']);
            $table->dropColumn(['bank_name_snapshot', 'account_no_snapshot', 'account_name_snapshot', 'transfer_ref', 'verified_by_admin_user_id', 'verified_at', 'admin_note']);
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropForeign(['verified_by_admin_user_id']);
            $table->dropColumn(['bank_name_snapshot', 'account_no_snapshot', 'account_name_snapshot', 'transfer_ref', 'verified_by_admin_user_id', 'verified_at', 'admin_note']);
        });
    }
};
