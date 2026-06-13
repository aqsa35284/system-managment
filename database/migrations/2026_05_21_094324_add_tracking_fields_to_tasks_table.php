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
            // 1. Kitne din employee ne is task par kaam kiya
            $table->integer('days_worked')->default(0)->after('status'); 
            
            // 2. Deadline time se upar kitne din zyada lagaye (Overdue Days)
            $table->integer('overdue_days')->default(0)->after('days_worked'); 
            
            // 3. Project/Task ki current half, progress ya completed stage
            $table->enum('completion_stage', ['not_started', 'progress', 'halfway', 'completed'])
                  ->default('not_started')
                  ->after('overdue_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Rollback ke waqt columns remove karne ke liye
            $table->dropColumn(['days_worked', 'overdue_days', 'completion_stage']);
        });
    }
};