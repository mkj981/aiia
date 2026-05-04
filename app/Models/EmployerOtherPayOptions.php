<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerOtherPayOptions extends Model
{
    use HasActivityLog;

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

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }
}
