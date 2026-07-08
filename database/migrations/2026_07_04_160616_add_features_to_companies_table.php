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
            // Check karega ke agar column pehle se nahi hai toh hi add kare
            if (!Schema::hasColumn('companies', 'features')) {
                // 'json' ki jagah 'text' use kar rahe hain taake koi database error na aaye
                $table->text('features')->nullable(); 
            }
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'features')) {
                $table->dropColumn('features');
            }
        });
    }
};
