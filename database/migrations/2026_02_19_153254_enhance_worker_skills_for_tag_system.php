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
        Schema::table('worker_skills', function (Blueprint $table) {
            // Add is_primary field to mark main skill
            if (!Schema::hasColumn('worker_skills', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('skill_id');
            }

            // Add sort_order for custom ordering
            if (!Schema::hasColumn('worker_skills', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_primary');
            }

            // Add notes/certification field for additional info
            if (!Schema::hasColumn('worker_skills', 'notes')) {
                $table->text('notes')->nullable()->after('experience_years');
            }

            // Add indexes for better performance
            $table->index(['worker_id', 'is_primary']);
            $table->index(['skill_id', 'proficiency_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_skills', function (Blueprint $table) {
            if (Schema::hasColumn('worker_skills', 'is_primary')) {
                $table->dropColumn('is_primary');
            }

            if (Schema::hasColumn('worker_skills', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('worker_skills', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
