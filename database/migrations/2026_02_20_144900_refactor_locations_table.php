<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get current table columns
        $columns = Schema::getColumnListing('locations');
        
        // Drop columns that exist
        if (in_array('province', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('province');
            });
        }
        
        if (in_array('district', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('district');
            });
        }
        
        if (in_array('postal_code', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('postal_code');
            });
        }
        
        if (in_array('latitude', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('latitude');
            });
        }
        
        if (in_array('longitude', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('longitude');
            });
        }
        
        // Add slug column if it doesn't exist
        if (!in_array('slug', $columns)) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('slug')->unique()->after('city');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Add back the dropped fields
            $table->string('province')->nullable()->after('slug');
            $table->string('district')->nullable()->after('province');
            $table->string('postal_code')->nullable()->after('district');
            $table->decimal('latitude', 10, 8)->nullable()->after('postal_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }
};
