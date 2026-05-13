<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('screenshots', function (Blueprint $table) {
            if (!Schema::hasColumn('screenshots', 'triggered_by_admin')) {
                $table->boolean('triggered_by_admin')->default(false)->after('window_title');
            }
        });
    }
    public function down(): void {
        Schema::table('screenshots', function (Blueprint $table) {
            $table->dropColumn('triggered_by_admin');
        });
    }
};