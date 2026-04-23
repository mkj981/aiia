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
        Schema::create('employer_default_pay_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('pay_schedule_id')->nullable()->constrained('pay_schedules')->nullOnDelete();
            $table->decimal('period_amount', 12, 2)->nullable();
            $table->decimal('annual_salary', 12, 2)->nullable();

            $table->string('pay_code', 50)->nullable();
            $table->string('pro_rata_adjustment', 50)->nullable();

            $table->boolean('allow_negative_net_pay')->default(false);
            $table->boolean('automatically_calculate_back_pay_for_new_starters')->default(false);
            $table->boolean('enable_paycode_validation')->default(false);
            $table->boolean('calculate_effective_date_salary_changes')->default(false);
            $table->boolean('group_paylines_on_payslip')->default(false);
            $table->boolean('sort_payroll_numbers_alpha_numerically')->default(false);

            $table->string('contracted_weeks', 50)->nullable();
            $table->string('full_time_contracted_weeks', 50)->nullable();
            $table->string('full_time_contracted_hours_per_week', 50)->nullable();

            $table->string('base_hourly_rate', 50)->nullable();
            $table->string('base_daily_rate', 50)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_default_pay_options');
    }
};
