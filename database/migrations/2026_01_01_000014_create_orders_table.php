<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('visitor_user_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('contract_id')->nullable()->unique();
            $table->string('status', 50);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('subtotal_idr');
            $table->unsignedBigInteger('platform_fee_idr')->default(0);
            $table->unsignedBigInteger('total_idr');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('visitor_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('restrict');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('service_categories')->onDelete('restrict');
            $table->index(['visitor_user_id', 'created_at']);
            $table->index(['worker_id', 'status']);
            $table->index(['agency_id', 'status']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
