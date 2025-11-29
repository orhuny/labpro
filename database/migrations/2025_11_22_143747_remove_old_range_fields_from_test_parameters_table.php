<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->dropColumn([
                'normal_range_min',
                'normal_range_max',
                'critical_low',
                'critical_high',
                'gender_specific',
                'male_normal_min',
                'male_normal_max',
                'female_normal_min',
                'female_normal_max',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->decimal('normal_range_min', 10, 2)->nullable()->after('unit');
            $table->decimal('normal_range_max', 10, 2)->nullable()->after('normal_range_min');
            $table->decimal('critical_low', 10, 2)->nullable()->after('normal_range_max');
            $table->decimal('critical_high', 10, 2)->nullable()->after('critical_low');
            $table->enum('gender_specific', ['none', 'male', 'female'])->default('none')->after('critical_high');
            $table->decimal('male_normal_min', 10, 2)->nullable()->after('gender_specific');
            $table->decimal('male_normal_max', 10, 2)->nullable()->after('male_normal_min');
            $table->decimal('female_normal_min', 10, 2)->nullable()->after('male_normal_max');
            $table->decimal('female_normal_max', 10, 2)->nullable()->after('female_normal_min');
        });
    }
};
