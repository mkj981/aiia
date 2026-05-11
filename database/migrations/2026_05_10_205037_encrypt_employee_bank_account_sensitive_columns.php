<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Laravel's encrypted cast stores ciphertext longer than short VARCHAR limits.
     * Existing plaintext account numbers are encrypted to match the model cast.
     */
    public function up(): void
    {
        Schema::table('employee_bank_accounts', function (Blueprint $table) {
            $table->text('account_number')->nullable()->change();
            $table->text('iban')->nullable()->after('account_number');
        });

        foreach (DB::table('employee_bank_accounts')->whereNotNull('account_number')->orderBy('id')->cursor() as $row) {
            $raw = $row->account_number;

            if (! is_string($raw) || $raw === '') {
                continue;
            }

            try {
                Crypt::decrypt($raw, false);
            } catch (DecryptException) {
                DB::table('employee_bank_accounts')->where('id', $row->id)->update([
                    'account_number' => Crypt::encrypt($raw, false),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Warning: rolling back after encrypted data exists will truncate account numbers
     * to 50 characters and drop IBAN. Prefer restoring from backup instead.
     */
    public function down(): void
    {
        Schema::table('employee_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('iban');
            $table->string('account_number', 50)->nullable()->change();
        });
    }
};
