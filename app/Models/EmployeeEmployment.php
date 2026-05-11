<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEmployment extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'job_title',
        'postcode',
        'start_date',
        'continuous_start_date',
        'payroll_code',
        'declaration',
        'change_of_payroll_id',
        'previous_payroll_code',
        'exclude_from_pay_runs',
        'pension_payroll_start_date',
        'annual_pension_amount',
        'works_in_freeport',
        'works_in_investment_zone',
        'leave_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'continuous_start_date' => 'date',
        'pension_payroll_start_date' => 'date',
        'leave_date' => 'date',
        'works_in_freeport' => 'boolean',
        'exclude_from_pay_runs' => 'boolean',
        'works_in_investment_zone' => 'boolean',

    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
