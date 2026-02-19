<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('worker_service_areas', function (Blueprint $table) {
            // Add is_primary field to mark main service area
            if (!Schema::hasColumn('worker_service_areas', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('location_id');
            }

            // Add service fee for this area
            if (!Schema::hasColumn('worker_service_areas', 'additional_fee_idr')) {
                $table->unsignedBigInteger('additional_fee_idr')->default(0)->after('radius_km');
            }

            // Add notes for this service area
            if (!Schema::hasColumn('worker_service_areas', 'notes')) {
                $table->text('notes')->nullable()->after('additional_fee_idr');
            }

            // Add is_active field
            if (!Schema::hasColumn('worker_service_areas', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('notes');
            }

            // Add indexes for better performance
            $table->index(['worker_id', 'is_primary']);
            $table->index(['location_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_service_areas', function (Blueprint $table) {
            if (Schema::hasColumn('worker_service_areas', 'is_primary')) {
                $table->dropColumn('is_primary');
            }

            if (Schema::hasColumn('worker_service_areas', 'additional_fee_idr')) {
                $table->dropColumn('additional_fee_idr');
            }

            if (Schema::hasColumn('worker_service_areas', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('worker_service_areas', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
