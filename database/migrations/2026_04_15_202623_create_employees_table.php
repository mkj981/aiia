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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();

            // Name
            $table->string('photo_path', 255)->nullable();
            $table->string('title', 20)->nullable();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);

            // Personal
            $table->date('date_of_birth')->nullable();
            $table->string('age', 20)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('marital_status', 50)->nullable();

            // Contact
            $table->string('email', 150)->nullable();
            $table->string('alternative_email', 150)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('mobile', 30)->nullable();

            // Identity
            $table->string('passport_number', 50)->nullable();
            $table->string('ni_number', 20)->nullable()->index();
            $table->string('previous_surname', 100)->nullable();

            // Address
            $table->string('address_line_1', 150)->nullable();
            $table->string('address_line_2', 150)->nullable();
            $table->string('address_line_3', 150)->nullable();
            $table->string('address_line_4', 150)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Partner
            $table->boolean('has_partner')->default(false);
            $table->string('partner_first_name', 100)->nullable();
            $table->string('partner_initials', 20)->nullable();
            $table->string('partner_last_name', 100)->nullable();
            $table->string('partner_ni_number', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
