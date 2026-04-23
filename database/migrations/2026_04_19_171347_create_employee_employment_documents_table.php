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
        Schema::create('employee_employment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_employment_id')->constrained('employee_employments')->cascadeOnDelete();
            $table->string('document_type', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_employment_documents');
    }
};
