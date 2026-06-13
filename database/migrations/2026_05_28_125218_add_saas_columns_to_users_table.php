<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Check if company_id does NOT exist, then create it
            if (!Schema::hasColumn('users', 'company_id')) {
                // Explicitly defining the table name 'companies' is safer
                $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            }

            // 2. Check if role does NOT exist, then create it
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin', 'employee'])->default('employee');
            }

            // 3. Check if status does NOT exist, then create it
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};