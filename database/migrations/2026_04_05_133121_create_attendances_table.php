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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // User ke saath link karne ke liye
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Attendance details
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            
            // ⭐ Time tracking columns
            $table->integer('work_time')->default(0); // Seconds mein total time save hoga
            $table->string('total_hours')->nullable(); // Display ke liye (e.g. "08:30")
            
            // Shift aur Status
            $table->string('shift')->nullable(); // Morning, Evening, Night
            $table->string('status')->default('present'); // present, idle, late, absent
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};