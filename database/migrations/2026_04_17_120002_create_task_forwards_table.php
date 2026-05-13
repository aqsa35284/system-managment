<?php
// database/migrations/xxxx_create_task_forwards_table.php
// Command: php artisan make:migration create_task_forwards_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_forwards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade'); // Kisne forward kiya
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');   // Kisko forward kiya
            $table->text('note')->nullable();   // Optional reason
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_forwards');
    }
    
};