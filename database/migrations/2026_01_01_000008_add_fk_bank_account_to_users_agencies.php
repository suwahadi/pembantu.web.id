<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('primary_bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->nullOnDelete();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->foreign('primary_bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['primary_bank_account_id']);
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['primary_bank_account_id']);
        });
    }
};
