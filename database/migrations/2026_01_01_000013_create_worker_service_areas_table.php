<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_service_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('radius_km')->default(5);
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->unique(['worker_id', 'location_id']);
            $table->index(['worker_id']);
            $table->index(['location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_service_areas');
    }
};
