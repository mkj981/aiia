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
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date')->nullable();
            $table->string('reference', 150)->nullable();
            $table->foreignId('pay_code_id')->nullable()->constrained('pay_codes')->nullOnDelete();
            $table->boolean('pause_payments')->default(false);
            $table->decimal('loan_amount', 12, 2)->default(0);
            $table->decimal('previously_paid', 12, 2)->default(0);
            $table->decimal('period_amount', 12, 2)->default(0);
            $table->decimal('amount_repaid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
