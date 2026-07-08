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
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained()->cascadeOnDelete();
        $table->string('invoice_number')->unique();
        $table->string('plan_name');
        $table->integer('total_users');
        $table->decimal('total_amount', 12, 2);
        $table->string('status')->default('unpaid'); // Jab bank details aayengi tab isay paid karenge
        $table->date('due_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
