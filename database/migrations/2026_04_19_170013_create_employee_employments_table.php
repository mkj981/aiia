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
        Schema::create('employee_employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('job_title', 150)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->date('start_date')->nullable();
            $table->date('continuous_start_date')->nullable();
            $table->string('payroll_code', 50)->nullable();
            $table->string('declaration', 100)->nullable();
            $table->string('change_of_payroll_id', 100)->nullable();
            $table->boolean('exclude_from_pay_runs')->default(false);

            $table->date('pension_payroll_start_date')->nullable();
            $table->string('annual_pension_amount', 50)->nullable();

            $table->boolean('works_in_freeport')->default(false);
            $table->boolean('works_in_investment_zone')->default(false);

            $table->date('leave_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_employments');
    }
};
