<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispute_evidences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id');
            $table->string('submitted_by_type', 50);
            $table->unsignedBigInteger('submitted_by_id');
            $table->string('file_path');
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');
            $table->index(['dispute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_evidences');
    }
};
