<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerBankAccount extends Model
{
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
        'reject_invalid_employee_bank_details' => 'boolean',
        'include_attachment_of_earnings' => 'boolean',
        'include_deductions' => 'boolean',
        'include_hmrc_payment' => 'boolean',
        'include_pensions' => 'boolean',

    ];
}
