<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->text('operator_feedback')->nullable()->after('description');
        $table->text('admin_feedback')->nullable()->after('operator_feedback');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['operator_feedback', 'admin_feedback']);
    });
}
};
