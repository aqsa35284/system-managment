<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_attendances', function (Blueprint $table) {
            $table->id();
            
            // Relational IDs
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            // Time Tracking
            $table->date('date'); // Aaj ki date
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            
            // Status track karne ke liye (Working, Break, waghera)
            $table->enum('status', ['working', 'on_break', 'completed', 'auto_checkout'])->default('working');
            
            // Total productive time (seconds mein)
            $table->integer('total_active_seconds')->default(0); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_attendances');
    }
};