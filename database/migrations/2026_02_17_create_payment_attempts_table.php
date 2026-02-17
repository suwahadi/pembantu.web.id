<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table untuk tracking payment attempts (Midtrans transactions)
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('midtrans_order_id', 100)->unique(); // Order ID dari Midtrans
            $table->string('transaction_id', 100)->nullable()->unique(); // Transaction ID dari Midtrans callback
            $table->unsignedBigInteger('amount_idr');
            $table->string('status', 50); // initiated, pending, settlement, expired, cancelled, denied, chargeback
            $table->json('raw_payload')->nullable(); // Simpan payload Midtrans untuk audit
            $table->timestamp('callback_received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['midtrans_order_id']);
            $table->index(['transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
