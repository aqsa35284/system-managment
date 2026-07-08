<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('companies', 'partner_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->foreignId('partner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('companies', 'partner_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropConstrainedForeignId('partner_id');
            });
        }
    }
};
