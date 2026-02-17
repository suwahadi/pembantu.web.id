<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('opened_by_user_id');
            $table->string('status', 50);
            $table->text('complaint');
            $table->string('decision', 50)->nullable();
            $table->unsignedBigInteger('refund_amount_idr')->default(0);
            $table->unsignedBigInteger('release_amount_idr')->default(0);
            $table->text('resolution_note')->nullable();
            $table->unsignedBigInteger('resolved_by_user_id')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('opened_by_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('resolved_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
