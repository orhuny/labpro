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
        Schema::create('test_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('normal_range_min', 10, 2)->nullable();
            $table->decimal('normal_range_max', 10, 2)->nullable();
            $table->decimal('critical_low', 10, 2)->nullable();
            $table->decimal('critical_high', 10, 2)->nullable();
            $table->enum('gender_specific', ['none', 'male', 'female'])->default('none');
            $table->decimal('male_normal_min', 10, 2)->nullable();
            $table->decimal('male_normal_max', 10, 2)->nullable();
            $table->decimal('female_normal_min', 10, 2)->nullable();
            $table->decimal('female_normal_max', 10, 2)->nullable();
            $table->enum('value_type', ['numeric', 'text', 'boolean', 'calculated'])->default('numeric');
            $table->text('calculation_formula')->nullable();
            $table->text('reference_values')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('test_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_parameters');
    }
};
