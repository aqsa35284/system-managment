<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_feedbacks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained(); // Employee ID
    $table->unsignedBigInteger('task_id');
    $table->text('update_note');
    $table->string('blockers')->nullable(); // Agar koi mushkil aa rahi hai
    $table->integer('completion_percentage')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_feedbacks');
    }
};
