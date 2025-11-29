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
        Schema::create('test_result_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_parameter_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->enum('flag', ['normal', 'high', 'low', 'critical_high', 'critical_low'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('test_result_id');
            $table->index('test_parameter_id');
            $table->unique(['test_result_id', 'test_parameter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_result_values');
    }
};
