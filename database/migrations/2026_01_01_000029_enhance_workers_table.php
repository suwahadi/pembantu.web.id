<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Add gender field (as per spec)
            if (!Schema::hasColumn('workers', 'gender')) {
                $table->string('gender', 10)->nullable()->after('name');
            }

            // Add birth_date field (as per spec)
            if (!Schema::hasColumn('workers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }

            // Add photo_path field (per spec - stored as photo_path not bio)
            if (!Schema::hasColumn('workers', 'photo_path')) {
                $table->string('photo_path', 255)->nullable()->after('birth_date');
            }

            // Rename bio to be kept as summary / additional field for backward compatibility
            // Note: 'bio' field kept as-is to avoid breaking existing queries

            // Add availability_status field (per spec)
            if (!Schema::hasColumn('workers', 'availability_status')) {
                $table->string('availability_status', 32)->default('available')->index()->after('experience_years');
            }

            // Drop is_available if exists (old implementation) - only after availability_status is added
            // We'll keep both for backward compatibility during transition
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'gender')) {
                $table->dropColumn('gender');
            }

            if (Schema::hasColumn('workers', 'birth_date')) {
                $table->dropColumn('birth_date');
            }

            if (Schema::hasColumn('workers', 'photo_path')) {
                $table->dropColumn('photo_path');
            }

            if (Schema::hasColumn('workers', 'availability_status')) {
                $table->dropColumn('availability_status');
            }
        });
    }
};
