<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Add location_id FK (required per spec)
            if (!Schema::hasColumn('agencies', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('business_license_no');
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('restrict');
                $table->index('location_id');
            }

            // Add cache columns for performance
            if (!Schema::hasColumn('agencies', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->nullable()->after('primary_bank_account_id');
            }

            if (!Schema::hasColumn('agencies', 'orders_completed_count')) {
                $table->unsignedInteger('orders_completed_count')->default(0)->after('rating_avg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }

            if (Schema::hasColumn('agencies', 'rating_avg')) {
                $table->dropColumn('rating_avg');
            }

            if (Schema::hasColumn('agencies', 'orders_completed_count')) {
                $table->dropColumn('orders_completed_count');
            }
        });
    }
};
