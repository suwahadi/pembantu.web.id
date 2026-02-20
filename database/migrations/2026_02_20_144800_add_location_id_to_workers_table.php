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
            // Add location_id as foreign key to locations table
            $table->foreignId('location_id')->nullable()->after('category_id')->constrained('locations')->onDelete('set null');
            
            // Add index for better performance
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id']);
            
            // Drop column
            $table->dropColumn('location_id');
        });
    }
};
