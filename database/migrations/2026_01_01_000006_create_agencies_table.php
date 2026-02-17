<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('company_name', 100);
            $table->text('description')->nullable();
            $table->string('business_license_no', 50)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('primary_bank_account_id')->nullable();
            $table->enum('verification_status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['verification_status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
