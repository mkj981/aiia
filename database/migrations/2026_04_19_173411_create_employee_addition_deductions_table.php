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
        Schema::create('employee_addition_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pay_code_id')->constrained('pay_codes')->cascadeOnDelete();

            $table->decimal('fixed_period_amount', 12, 2)->nullable();
            $table->boolean('gross_up_target_net')->default(false);

            $table->string('pro_rata_adjustment', 50)->nullable();
            $table->string('description', 255)->nullable();

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_addition_deductions');
    }
};
