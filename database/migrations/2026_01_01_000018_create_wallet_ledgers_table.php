<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('entry_key', 255)->unique();
            $table->string('debit_account', 50);
            $table->string('credit_account', 50);
            $table->unsignedBigInteger('amount_idr');
            $table->string('ref_type', 50);
            $table->unsignedBigInteger('ref_id');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['debit_account']);
            $table->index(['credit_account']);
            $table->index(['ref_type', 'ref_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
