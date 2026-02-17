<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();

            $table->unique(['province', 'city', 'district']);
            $table->index(['city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
