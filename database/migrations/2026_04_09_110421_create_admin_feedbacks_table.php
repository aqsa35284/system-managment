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
       Schema::create('admin_feedbacks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->references('id')->on('users'); // Feedback dene wala admin
    $table->foreignId('employee_id')->references('id')->on('users'); // Jis employee ko feedback mil raha hai
    $table->unsignedBigInteger('task_id'); 
    $table->text('review_note');
    $table->integer('rating_score')->nullable(); // 1 to 10 marks
    $table->enum('status', ['excellent', 'good', 'needs_improvement']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_feedbacks');
    }
};
