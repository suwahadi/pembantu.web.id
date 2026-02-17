<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_service_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->enum('pricing_type', ['hourly', 'daily', 'weekly', 'monthly', 'project'])->default('daily');
            $table->unsignedBigInteger('price_idr');
            $table->unsignedInteger('min_duration')->nullable();
            $table->unsignedInteger('max_duration')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->index(['worker_id']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_service_pricings');
    }
};
