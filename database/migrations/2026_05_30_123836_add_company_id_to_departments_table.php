<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check lagana hamesha safe rehta hai
        if (Schema::hasTable('departments') && !Schema::hasColumn('departments', 'company_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'company_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};