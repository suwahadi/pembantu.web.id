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
        Schema::table('workers', function (Blueprint $table) {
            // Remove location_id - will use worker_service_areas table instead
            if (Schema::hasColumn('workers', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }

            // Remove min_price_idr - will use worker_service_pricings table instead
            if (Schema::hasColumn('workers', 'min_price_idr')) {
                $table->dropColumn('min_price_idr');
            }

            // Remove skills text field - will use worker_skills table instead (tag-like system)
            if (Schema::hasColumn('workers', 'skills')) {
                $table->dropColumn('skills');
            }

            // Remove default_scheme - pricing scheme will be managed in worker_service_pricings
            if (Schema::hasColumn('workers', 'default_scheme')) {
                $table->dropColumn('default_scheme');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Add back location_id
            $table->unsignedBigInteger('location_id')->nullable()->after('bio');
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();

            // Add back min_price_idr
            $table->unsignedBigInteger('min_price_idr')->default(0)->after('verification_status');

            // Add back skills text field
            $table->text('skills')->nullable()->after('bio');

            // Add back default_scheme
            $table->string('default_scheme', 20)->default('BULANAN')->after('min_price_idr');
        });
    }
};
