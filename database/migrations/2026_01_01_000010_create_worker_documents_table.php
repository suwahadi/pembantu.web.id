<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->string('document_type', 50);
            $table->string('document_no', 50);
            $table->string('file_path');
            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->enum('verification_status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->index(['worker_id']);
            $table->index(['document_type']);
            $table->unique(['worker_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_documents');
    }
};
