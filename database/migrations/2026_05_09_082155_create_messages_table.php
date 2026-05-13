<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sender_id');
        $table->unsignedBigInteger('receiver_id')->nullable(); // For Direct Messages
        $table->string('group_name')->nullable();             // For Group Chats
        $table->text('message')->nullable();
        $table->string('file_path')->nullable();              // For Images/Docs
        $table->string('file_name')->nullable();
        $table->string('voice_path')->nullable();             // For Voice Notes
        $table->string('message_type')->default('text');      // text, file, voice
        $table->boolean('is_read')->default(false);
        $table->timestamps();

        $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
