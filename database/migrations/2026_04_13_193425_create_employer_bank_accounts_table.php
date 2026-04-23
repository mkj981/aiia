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
        Schema::create('employer_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();

            $table->string('bank_name', 150)->nullable();
            $table->string('bank_branch', 150)->nullable();
            $table->string('bank_reference', 100)->nullable();

            $table->string('account_name', 150)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('sort_code', 20)->nullable();

            $table->string('building_society_reference', 100)->nullable();
            $table->string('country_of_bank', 100);
            $table->string('iban', 50)->nullable();
            $table->string('swift_bic', 20)->nullable();

            $table->foreignId('bank_payment_csv_format_id')->nullable()->constrained('bank_csv_formats')->nullOnDelete();
            $table->string('bacs_sun', 20)->nullable();
            $table->string('payment_reference_format', 255)->nullable();

            $table->boolean('reject_invalid_employee_bank_details')->default(false);
            $table->boolean('include_attachment_of_earnings')->default(false);
            $table->boolean('include_deductions')->default(false);
            $table->boolean('include_hmrc_payment')->default(false);
            $table->boolean('include_pensions')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_bank_accounts');
    }
};
