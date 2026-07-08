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
    Schema::create('subscription_plans', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., Pro, Advanced
        $table->string('slug')->unique(); // e.g., pro, advanced
        $table->integer('price')->default(0); // Base price
        $table->integer('extra_user_rate')->default(0); 
        $table->integer('retention_days')->default(7); // Data delete hone ka time
        
        // ⭐ THE MAGIC COLUMN: Ismein array save hogi (e.g., ["chat", "screenshots", "ai_radar"])
        $table->json('features')->nullable(); 
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
