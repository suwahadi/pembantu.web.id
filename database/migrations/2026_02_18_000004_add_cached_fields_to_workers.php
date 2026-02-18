<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (!Schema::hasColumn('workers', 'min_price_idr')) {
                $table->unsignedBigInteger('min_price_idr')->default(0)->after('verification_status');
            }
            if (!Schema::hasColumn('workers', 'skills')) {
                $table->text('skills')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('workers', 'default_scheme')) {
                $table->string('default_scheme', 20)->default('BULANAN')->after('min_price_idr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['min_price_idr', 'skills', 'default_scheme']);
        });
    }
};
