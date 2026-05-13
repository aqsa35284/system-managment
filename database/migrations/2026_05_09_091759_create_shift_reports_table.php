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
        Schema::create('shift_reports', function (Blueprint $table) {
            $table->id();
            // Employee identification
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Shift details
            $table->string('shift_name'); // e.g., Morning, Evening
            $table->integer('total_seconds')->default(0);
            $table->decimal('ai_percentage', 5, 2)->default(0);
            $table->integer('pulse_count')->default(0);
            
            // Task summary from employee briefing
            $table->text('task_summary')->nullable();
            
            // Date for filtering (dashboard needs this)
            $table->date('report_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_reports');
    }
};