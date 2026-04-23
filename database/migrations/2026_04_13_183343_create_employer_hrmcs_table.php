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
        Schema::create('employer_hrmcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();

            // PAYE Reference: 120 / S1510
            $table->string('paye_office_number', 20)->nullable();
            $table->string('paye_reference', 50)->nullable();

            $table->string('accounts_office_reference', 50)->nullable();
            $table->string('econ_number', 50)->nullable();
            $table->string('utr', 50)->nullable();
            $table->string('corporation_tax_reference', 50)->nullable();

            //Payment Schedule
            $table->string('payment_schedule', 20)->default('monthly'); // monthly, quarterly
            $table->boolean('carry_forward_unpaid_liabilities')->default(false);
            $table->string('payment_date_type', 31)->default('pay_date');
            $table->unsignedTinyInteger('payment_day_of_month')->nullable();

            //Small Employers Relief
            $table->boolean('qualifies_for_small_employers_relief')->default(false);

            // Employment Allowance
            $table->boolean('eligible_for_employment_allowance')->default(false);
            $table->decimal('employment_allowance_max_claim', 12, 2)->nullable();
            $table->boolean('include_employment_allowance_on_monthly_journal')->default(false);

            //Apprenticeship Levy
            $table->boolean('required_to_pay_apprenticeship_levy')->default(false);
            $table->decimal('apprenticeship_levy_allowance', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_hrmcs');
    }
};
