<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_result_values', function (Blueprint $table) {
            // Add new boolean column
            $table->boolean('is_outside_normal_range')->default(false)->after('value');
        });
        
        // Migrate existing data: set is_outside_normal_range to true if flag is not 'normal'
        DB::statement("UPDATE test_result_values SET is_outside_normal_range = 1 WHERE flag IS NOT NULL AND flag != 'normal'");
        
        Schema::table('test_result_values', function (Blueprint $table) {
            // Drop old flag column
            $table->dropColumn('flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_result_values', function (Blueprint $table) {
            // Re-add flag column
            $table->enum('flag', ['normal', 'high', 'low', 'critical_high', 'critical_low'])->nullable()->after('value');
            
            // Migrate data back: set flag based on is_outside_normal_range
            DB::statement("UPDATE test_result_values SET flag = 'normal' WHERE is_outside_normal_range = 0");
            DB::statement("UPDATE test_result_values SET flag = 'high' WHERE is_outside_normal_range = 1");
            
            // Drop new column
            $table->dropColumn('is_outside_normal_range');
        });
    }
};
