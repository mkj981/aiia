<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeerOtherPayOptions extends Model
{
    protected $fillable = [
        'employer_id',
        'student_loan_plan',
        'postgrad_loan',
        'hours_normally_worked_band',
        'payment_method',
        'vehicle_type',
        'withhold_tax_refund_if_gross_pay_zero',
        'off_payroll_worker',
        'irregular_payment_pattern',
        'non_individual',
        'exclude_from_rti_submissions',
    ];


    protected $casts = [
        'withhold_tax_refund_if_gross_pay_zero'     => 'boolean',
        'off_payroll_worker'                        => 'boolean',
        'irregular_payment_pattern'                 => 'boolean',
        'non_individual'                            => 'boolean',
        'exclude_from_rti_submissions'              => 'boolean',
    ];
}
