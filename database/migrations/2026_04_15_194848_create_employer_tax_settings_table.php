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
        Schema::create('employer_tax_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();

            $table->string('tax_code', 50)->nullable();
            $table->boolean('week1_month1')->default(false);

            $table->foreignId('ni_id')->nullable()->constrained('nis')->nullOnDelete();
            $table->boolean('ni_secondary_class_nics_not_payable')->default(false);
            $table->boolean('enable_foreign_tax_credit')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_tax_settings');
    }
};
