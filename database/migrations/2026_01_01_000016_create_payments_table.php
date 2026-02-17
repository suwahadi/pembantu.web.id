<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('midtrans_order_id', 100)->unique();
            $table->string('transaction_id', 100)->nullable()->unique();
            $table->string('status', 50);
            $table->unsignedBigInteger('amount_idr');
            $table->string('payment_method', 50)->nullable();
            $table->json('request_payload')->nullable();
            $table->json('last_callback_payload')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
