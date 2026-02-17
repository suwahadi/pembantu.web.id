<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('reviewer_user_id');
            $table->unsignedBigInteger('reviewed_worker_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('reviewer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->index(['order_id']);
            $table->index(['reviewed_worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
