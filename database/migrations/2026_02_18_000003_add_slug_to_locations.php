<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'slug')) {
                $table->string('slug', 80)->nullable()->after('city');
                $table->unique('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
