<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add currency field (per spec: char(3) default 'IDR')
            if (!Schema::hasColumn('orders', 'currency')) {
                $table->char('currency', 3)->default('IDR')->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
