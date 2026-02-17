<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payee_type', 50);
            $table->unsignedBigInteger('payee_id');
            $table->unsignedBigInteger('bank_account_id');
            $table->unsignedBigInteger('amount_idr');
            $table->string('status', 50);
            $table->string('reason', 255)->nullable();
            $table->string('proof_file_path', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('restrict');
            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
