<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pehle check karein ke tasks table mojood hai ya nahi
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Phir check karein ke column pehle se toh nahi bana hua
                if (!Schema::hasColumn('tasks', 'completion_percentage')) {
                    $table->integer('completion_percentage')->default(0);
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (Schema::hasColumn('tasks', 'completion_percentage')) {
                    $table->dropColumn('completion_percentage');
                }
            });
        }
    }
};