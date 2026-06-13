<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->enum('status', ['active', 'suspended', 'expired'])->default('active');
        $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
        $table->date('subscription_ends_at')->nullable();
        $table->integer('max_employees')->default(10); // Limit system
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
