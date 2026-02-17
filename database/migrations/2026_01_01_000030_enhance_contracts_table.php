<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Add work_days field (comma-separated or JSON per spec: MON,TUE,WED, etc)
            if (!Schema::hasColumn('contracts', 'work_days')) {
                $table->string('work_days', 40)->nullable()->after('end_date');
            }

            // Add work_hours field (e.g., "08:00-17:00")
            if (!Schema::hasColumn('contracts', 'work_hours')) {
                $table->string('work_hours', 40)->nullable()->after('work_days');
            }

            // Add location_id FK (required per spec)
            if (!Schema::hasColumn('contracts', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('job_scope');
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('restrict');
            }

            // Add scope_of_work (may appear similar to job_scope, but kept for spec compliance)
            // Keeping job_scope as-is for backward compatibility, adding scope_of_work if needed
            if (!Schema::hasColumn('contracts', 'scope_of_work')) {
                $table->text('scope_of_work')->nullable()->after('location_id');
            }

            // Add overtime_rules field (optional per spec)
            if (!Schema::hasColumn('contracts', 'overtime_rules')) {
                $table->text('overtime_rules')->nullable()->after('scope_of_work');
            }

            // Add financial fields per spec
            if (!Schema::hasColumn('contracts', 'total_price_idr')) {
                $table->unsignedBigInteger('total_price_idr')->default(0)->after('overtime_rules');
            }

            if (!Schema::hasColumn('contracts', 'platform_fee_idr')) {
                $table->unsignedBigInteger('platform_fee_idr')->default(0)->after('total_price_idr');
            }

            if (!Schema::hasColumn('contracts', 'other_fee_idr')) {
                $table->unsignedBigInteger('other_fee_idr')->default(0)->after('platform_fee_idr');
            }

            // Add status field (per spec: draft/signed_by_visitor/signed_by_agency/active/completed/cancelled)
            if (!Schema::hasColumn('contracts', 'status')) {
                $table->string('status', 32)->default('draft')->index()->after('other_fee_idr');
            }

            // Add location_id index if location_id exists
            if (Schema::hasColumn('contracts', 'location_id')) {
                try {
                    $table->index('location_id');
                } catch (\Exception $e) {
                    // Index may already exist
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'work_days')) {
                $table->dropColumn('work_days');
            }

            if (Schema::hasColumn('contracts', 'work_hours')) {
                $table->dropColumn('work_hours');
            }

            if (Schema::hasColumn('contracts', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }

            if (Schema::hasColumn('contracts', 'scope_of_work')) {
                $table->dropColumn('scope_of_work');
            }

            if (Schema::hasColumn('contracts', 'overtime_rules')) {
                $table->dropColumn('overtime_rules');
            }

            if (Schema::hasColumn('contracts', 'total_price_idr')) {
                $table->dropColumn('total_price_idr');
            }

            if (Schema::hasColumn('contracts', 'platform_fee_idr')) {
                $table->dropColumn('platform_fee_idr');
            }

            if (Schema::hasColumn('contracts', 'other_fee_idr')) {
                $table->dropColumn('other_fee_idr');
            }

            if (Schema::hasColumn('contracts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
