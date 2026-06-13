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
       Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('super_admin_id'); // Kaun audit kar raha hai
    $table->unsignedBigInteger('company_id');     // Kis company ka admin
    $table->string('action');                    // Kya kiya (e.g., "Updated Task #5")
    $table->string('url');                       // Kahan kiya
    $table->ipAddress('ip');                     // IP Address
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
