<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role column ko string mein convert kar rahe hain taake koi bhi naya role easily add ho sake
            $table->string('role')->default('employee')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback ki soorat mein wapas enum
            $table->enum('role', ['super_admin', 'admin', 'employee'])->default('employee')->change();
        });
    }
};