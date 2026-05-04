<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerDefaultPayOption extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employer_id',

        'pay_schedule_id',
        'pay_basis_id',

        'pay_code',
        'pro_rata_adjustment',

        'period_amount',
        'annual_salary',

        'allow_negative_net_pay',
        'automatically_calculate_back_pay_for_new_starters',
        'enable_paycode_validation',
        'calculate_effective_date_salary_changes',
        'group_paylines_on_payslip',
        'sort_payroll_numbers_alpha_numerically',

        'contracted_weeks',
        'full_time_contracted_weeks',
        'full_time_contracted_hours_per_week',

        'base_hourly_rate',
        'base_daily_rate',
    ];


    protected $casts = [
        'allow_negative_net_pay'                            => 'boolean',
        'automatically_calculate_back_pay_for_new_starters' => 'boolean',
        'enable_paycode_validation'                         => 'boolean',
        'calculate_effective_date_salary_changes'           => 'boolean',
        'group_paylines_on_payslip'                         => 'boolean',
        'sort_payroll_numbers_alpha_numerically'            => 'boolean',
    ];

    public function paySchedule(): BelongsTo
    {
        return $this->belongsTo(PaySchedule::class, 'pay_schedule_id');
    }

    public function payBasis(): BelongsTo
    {
        return $this->belongsTo(PayBasis::class, 'pay_bases_id');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
