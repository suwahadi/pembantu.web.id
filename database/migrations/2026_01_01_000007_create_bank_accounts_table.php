<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->string('bank_code', 10);
            $table->string('bank_name', 100);
            $table->string('account_no', 30);
            $table->string('account_name', 120);
            $table->enum('verified_status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['owner_type', 'owner_id', 'account_no']);
            // Note: morphs() already creates an index on owner_type, owner_id
            $table->index(['verified_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
