<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Kis user (Partner/Employee/Admin) ne action kiya
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('action'); // created, updated, deleted, login, logout
            $table->string('subject_type'); // Model name (e.g., App\Models\Company)
            $table->unsignedBigInteger('subject_id'); // Model ki ID
            $table->string('description'); // Sahal samajh aane wali details
            $table->json('old_values')->nullable(); // Pehle kya data tha
            $table->json('new_values')->nullable(); // Baad mein kya data hua
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};