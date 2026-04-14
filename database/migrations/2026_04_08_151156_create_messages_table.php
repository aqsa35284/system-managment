<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up(): void
{
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sender_id');
        $table->unsignedBigInteger('receiver_id')->nullable(); // Ye nullable hona zaroori hai
        $table->string('group_name')->nullable(); 
        $table->text('message');
        $table->string('message_type')->default('text');
        $table->boolean('is_read')->default(false);
        $table->timestamps();

        // Foreign keys constraints ko nullable handle karne ke liye aise likhein
        $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        // receiver_id null ho sakti hai group chat ke liye
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