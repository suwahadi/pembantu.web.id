<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no', 50)->unique();
            $table->unsignedBigInteger('order_id')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('job_scope');
            $table->text('location_address');
            $table->longText('terms_conditions')->nullable();
            $table->boolean('visitor_signed')->default(false);
            $table->timestamp('visitor_signed_at')->nullable();
            $table->boolean('agency_signed')->default(false);
            $table->timestamp('agency_signed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
