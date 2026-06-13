<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Enterprise level scaling: Making searches lightning fast
            $table->index('company_id');
            $table->index('super_admin_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['super_admin_id']);
            $table->dropIndex(['created_at']);
        });
    }
};