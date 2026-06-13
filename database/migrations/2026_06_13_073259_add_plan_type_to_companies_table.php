<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('companies', function (Blueprint $table) {
        // SaaS pricing structure ke mutabiq enum column add kiya
        $table->enum('plan_type', ['starter', 'basic', 'pro', 'advanced', 'private_label', 'enterprise'])->default('starter')->after('status');
    });
}

public function down()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn('plan_type');
    });
}
   
   
};
