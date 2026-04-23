<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_pay_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('pay_schedule_id')->nullable()->constrained('pay_schedules')->nullOnDelete();
            $table->foreignId('pay_basis_id')->nullable()->constrained('pay_bases')->nullOnDelete();

            $table->string('working_pattern', 100)->nullable();

            $table->decimal('monthly_amount', 12, 2)->nullable();
            $table->decimal('annual_salary', 12, 2)->nullable();

            $table->string('pay_code', 50)->nullable();
            $table->string('pro_rata_adjustment', 50)->nullable();

            $table->decimal('base_hourly_rate', 12, 2)->nullable();
            $table->decimal('base_daily_rate', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_pay_options');
    }
};
