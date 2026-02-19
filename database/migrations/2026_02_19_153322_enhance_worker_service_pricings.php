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
        Schema::table('worker_service_pricings', function (Blueprint $table) {
            // Add is_default field to mark default pricing
            if (!Schema::hasColumn('worker_service_pricings', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('pricing_type');
            }

            // Add sort_order for custom ordering
            if (!Schema::hasColumn('worker_service_pricings', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_default');
            }

            // Add minimum_order_amount
            if (!Schema::hasColumn('worker_service_pricings', 'min_order_amount')) {
                $table->unsignedBigInteger('min_order_amount')->default(0)->after('price_idr');
            }

            // Add effective_date and expiry_date for pricing validity
            if (!Schema::hasColumn('worker_service_pricings', 'effective_date')) {
                $table->date('effective_date')->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('worker_service_pricings', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('effective_date');
            }

            // Add indexes for better performance
            $table->index(['worker_id', 'is_default']);
            $table->index(['pricing_type', 'is_active']);
            $table->index(['effective_date', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_service_pricings', function (Blueprint $table) {
            if (Schema::hasColumn('worker_service_pricings', 'is_default')) {
                $table->dropColumn('is_default');
            }

            if (Schema::hasColumn('worker_service_pricings', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('worker_service_pricings', 'min_order_amount')) {
                $table->dropColumn('min_order_amount');
            }

            if (Schema::hasColumn('worker_service_pricings', 'effective_date')) {
                $table->dropColumn('effective_date');
            }

            if (Schema::hasColumn('worker_service_pricings', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
        });
    }
};
