<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerHrmc extends Model
{
    protected $fillable = [
        'employer_id',

        'paye_office_number',
        'paye_reference',

        'accounts_office_reference',
        'econ_number',
        'utr',
        'corporation_tax_reference',

        'payment_schedule',
        'carry_forward_unpaid_liabilities',
        'payment_date_type',
        'payment_day_of_month',

        'qualifies_for_small_employers_relief',

        'eligible_for_employment_allowance',
        'employment_allowance_max_claim',
        'include_employment_allowance_on_monthly_journal',

        'required_to_pay_apprenticeship_levy',
        'apprenticeship_levy_allowance',
    ];


    protected $casts = [

        // Booleans
        'carry_forward_unpaid_liabilities'                  => 'boolean',
        'qualifies_for_small_employers_relief'              => 'boolean',
        'eligible_for_employment_allowance'                 => 'boolean',
        'include_employment_allowance_on_monthly_journal'   => 'boolean',
        'required_to_pay_apprenticeship_levy'               => 'boolean',

        // Financial
        'employment_allowance_max_claim'                    => 'decimal:2',
        'apprenticeship_levy_allowance'                     => 'decimal:2',

        // Numbers
        'payment_day_of_month'                              => 'integer',
    ];
}
