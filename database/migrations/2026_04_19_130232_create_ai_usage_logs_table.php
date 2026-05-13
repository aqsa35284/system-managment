<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ai_tool');                        // e.g. CHATGPT, CLAUDE, YOUTUBE
            $table->integer('duration_seconds')->default(0);  // accumulated usage time
            $table->string('activity_title')->nullable();     // window title context
            $table->date('log_date');                         // tracking date
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};