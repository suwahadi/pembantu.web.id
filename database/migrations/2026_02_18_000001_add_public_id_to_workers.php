<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (!Schema::hasColumn('workers', 'public_id')) {
                $table->string('public_id', 16)->nullable()->after('id');
                $table->unique('public_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'public_id')) {
                $table->dropUnique(['public_id']);
                $table->dropColumn('public_id');
            }
        });
    }
};
