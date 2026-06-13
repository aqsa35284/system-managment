<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Ek array mein sab tables ke naam likhein jo company se link hongi
        $tables = [
            'projects', 'tasks', 'messages', 'employee_feedbacks', 
            'admin_feedbacks', 'task_forwards', 'attendances', 
            'screenshots', 'ai_usage_logs', 'shift_reports'
        ];

        foreach ($tables as $tableName) {
            // Check lagayein ke table exist karta hai aur column pehle se nahi hai
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'projects', 'tasks', 'messages', 'employee_feedbacks', 
            'admin_feedbacks', 'task_forwards', 'attendances', 
            'screenshots', 'ai_usage_logs', 'shift_reports'
        ];

        foreach ($tables as $tableName) {
            // Check karein ke table aur column mojood hain
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    // Pehle Foreign Key ka relation torein, phir column udayen
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};