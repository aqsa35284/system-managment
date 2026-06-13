<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Companies Table Update
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'max_employees')) {
                $table->integer('max_employees')->default(15); // ⭐ Quota Limit
            }
            if (!Schema::hasColumn('companies', 'status')) {
                $table->enum('status', ['active', 'suspended'])->default('active');
            }
        });

        // 2. Users Table Update
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin', 'employee'])->default('employee');
            }
        });
    }

    public function down()
    {
        // ... (down method can be left empty for this safe update)
    }
};