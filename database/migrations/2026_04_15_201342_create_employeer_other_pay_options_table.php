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
        Schema::create('employer_other_pay_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();

            $table->string('student_loan_plan', 50)->nullable();
            $table->string('postgrad_loan', 50)->nullable();
            $table->string('hours_normally_worked_band', 50)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('vehicle_type', 50)->nullable();

            $table->boolean('withhold_tax_refund_if_gross_pay_zero')->default(false);
            $table->boolean('off_payroll_worker')->default(false);
            $table->boolean('irregular_payment_pattern')->default(false);
            $table->boolean('non_individual')->default(false);
            $table->boolean('exclude_from_rti_submissions')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_other_pay_options');
    }
};
