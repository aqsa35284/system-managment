<?php

// ============================================================
// FILE 1: Migration
// Path: database/migrations/xxxx_add_screenshot_trigger_to_users_table.php
// Command: php artisan make:migration add_screenshot_trigger_to_users_table
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Sirf add karo agar already exist nahi karta
            if (!Schema::hasColumn('users', 'screenshot_trigger')) {
                $table->boolean('screenshot_trigger')->default(false)->after('profile_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('screenshot_trigger');
        });
    }
};

