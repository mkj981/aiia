<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerBankAccount extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employer_id',

        'bank_name',
        'bank_branch',
        'bank_reference',

        'account_name',
        'account_number',
        'sort_code',

        'building_society_reference',
        'country_of_bank',
        'iban',
        'swift_bic',

        'bank_payment_csv_format_id',
        'bacs_sun',
        'payment_reference_format',

        'reject_invalid_employee_bank_details',
        'include_attachment_of_earnings',
        'include_deductions',
        'include_hmrc_payment',
        'include_pensions',
    ];

    protected $casts = [
        'account_number' => 'encrypted',
        'iban' => 'encrypted',

        'reject_invalid_employee_bank_details' => 'boolean',
        'include_attachment_of_earnings' => 'boolean',
        'include_deductions' => 'boolean',
        'include_hmrc_payment' => 'boolean',
        'include_pensions' => 'boolean',

    ];

    public function bankPaymentCsvFormat(): BelongsTo
    {
        return $this->belongsTo(BankCsvFormat::class, 'bank_payment_csv_format_id');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
