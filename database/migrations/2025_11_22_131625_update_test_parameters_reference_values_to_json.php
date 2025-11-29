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
        // Change reference_values from text to JSON
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->json('reference_ranges')->nullable()->after('reference_values');
        });

        // Migrate existing data if any
        DB::statement('UPDATE test_parameters SET reference_ranges = NULL WHERE reference_values IS NULL');
        
        // Drop old column
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->dropColumn('reference_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->text('reference_values')->nullable()->after('calculation_formula');
        });

        // Convert JSON back to text (simplified)
        DB::statement('UPDATE test_parameters SET reference_values = NULL WHERE reference_ranges IS NULL');

        Schema::table('test_parameters', function (Blueprint $table) {
            $table->dropColumn('reference_ranges');
        });
    }
};
