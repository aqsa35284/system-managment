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
        Schema::table('users', function (Blueprint $table) {
            // 1. Profile Image (Safe Check)
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('email');
            }

            // 2. Salary (Safe Check)
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable();
            }

            // 3. Department ID (ASLI MASLA: Agar ye nahi hai toh pehle ye banayein)
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable();
            }

            // 4. Join Date (Bina 'after' ke taaki error na aaye)
            if (!Schema::hasColumn('users', 'join_date')) {
                $table->date('join_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'salary', 'join_date', 'department_id']);
        });
    }
};