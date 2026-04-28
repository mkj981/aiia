<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Laravel's encrypted cast stores a serialized ciphertext longer than short VARCHAR limits.
     */
    public function up(): void
    {
        Schema::table('employer_bank_accounts', function (Blueprint $table) {
            $table->text('account_number')->nullable()->change();
            $table->text('iban')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_bank_accounts', function (Blueprint $table) {
            $table->string('account_number', 50)->nullable()->change();
            $table->string('iban', 50)->nullable()->change();
        });
    }
};
