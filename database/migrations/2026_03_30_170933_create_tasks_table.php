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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
        $table->enum('status', ['to_do', 'in_progress', 'review', 'completed'])->default('to_do');
        $table->date('due_date')->nullable();
        
        // Relationships
        $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Kis project ka task hai
        $table->foreignId('user_id')->constrained()->onDelete('cascade');    // Kis employee ko assign hua hai
        
        $table->timestamps();
    });
}
};
