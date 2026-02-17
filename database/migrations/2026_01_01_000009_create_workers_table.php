<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name', 100);
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('verification_status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_completed_orders')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('service_categories')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->index(['agency_id']);
            $table->index(['category_id']);
            $table->index(['verification_status']);
            $table->index(['is_available']);
            $table->index(['rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
