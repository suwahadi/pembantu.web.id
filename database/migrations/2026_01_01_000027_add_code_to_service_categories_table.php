<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            // Add code field (nullable first to avoid constraint violation)
            if (!Schema::hasColumn('service_categories', 'code')) {
                $table->string('code', 32)->nullable()->after('id');
            }
        });

        // Populate code based on slug or name for existing records
        $categories = DB::table('service_categories')->get();
        foreach ($categories as $cat) {
            $code = $cat->slug ? Str::upper(str_replace('-', '_', $cat->slug)) : Str::upper(str_replace(' ', '_', $cat->name));
            DB::table('service_categories')->where('id', $cat->id)->update(['code' => $code]);
        }

        // Now add unique constraint
        Schema::table('service_categories', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            if (Schema::hasColumn('service_categories', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
