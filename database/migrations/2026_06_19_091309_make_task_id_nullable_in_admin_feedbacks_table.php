<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_feedbacks', function (Blueprint $table) {
            // Hum database ko bata rahe hain ke task_id ko null allow karo
            $table->unsignedBigInteger('task_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admin_feedbacks', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id')->nullable(false)->change();
        });
    }
};