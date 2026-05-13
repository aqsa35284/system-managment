<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ai_tool');
            $table->integer('duration_seconds')->default(0);
            $table->string('activity_title')->nullable();
            $table->date('log_date');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ai_usage_logs');
    }
};