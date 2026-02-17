<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('skill_id');
            $table->enum('proficiency_level', ['basic', 'intermediate', 'advanced', 'expert'])->default('basic');
            $table->integer('experience_years')->default(0);
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('service_skills')->onDelete('cascade');
            $table->unique(['worker_id', 'skill_id']);
            $table->index(['worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_skills');
    }
};
