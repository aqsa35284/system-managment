<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Safe Check: Agar column nahi hai, toh hi banao
            if (!Schema::hasColumn('companies', 'phone')) { 
                $table->string('phone')->nullable(); 
            }
            if (!Schema::hasColumn('companies', 'status')) { 
                $table->enum('status', ['active', 'suspended', 'expired'])->default('active'); 
            }
            if (!Schema::hasColumn('companies', 'payment_status')) { 
                $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid'); 
            }
            if (!Schema::hasColumn('companies', 'subscription_ends_at')) { 
                $table->date('subscription_ends_at')->nullable(); 
            }
            if (!Schema::hasColumn('companies', 'max_employees')) { 
                $table->integer('max_employees')->default(15); 
            }
        });
    }

    public function down()
    {
        // Rollback ke liye
        Schema::table('companies', function (Blueprint $table) {
            $columns = ['phone', 'status', 'payment_status', 'subscription_ends_at', 'max_employees'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('companies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};