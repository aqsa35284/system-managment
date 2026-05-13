<?php
// database/migrations/xxxx_add_forwarded_to_tasks_table.php
// Command: php artisan make:migration add_forwarded_to_tasks_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'forwarded_to')) {
                // Kisko assign hai abhi
                $table->unsignedBigInteger('forwarded_to')->nullable()->after('user_id');
                $table->foreign('forwarded_to')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'original_user_id')) {
                // Original owner kaun tha
                $table->unsignedBigInteger('original_user_id')->nullable()->after('forwarded_to');
                $table->foreign('original_user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['forwarded_to']);
            $table->dropForeign(['original_user_id']);
            $table->dropColumn(['forwarded_to', 'original_user_id']);
        });
    }
};