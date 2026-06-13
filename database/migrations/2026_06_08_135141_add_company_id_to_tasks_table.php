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
        Schema::table('tasks', function (Blueprint $table) {
            // Check lagaya hai taake agar column pehle se bana hua hai toh dobara add karne par crash na ho
            if (!Schema::hasColumn('tasks', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'company_id')) {
                $table->dropColumn('company_id');
            }
        });
    }
};