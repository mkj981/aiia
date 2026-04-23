<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayOption extends Model
{
    protected $fillable = [
        'employee_id',
        'pay_schedule_id',
        'pay_basis_id',
        'working_pattern',
        'monthly_amount',
        'annual_salary',
        'pay_code',
        'pro_rata_adjustment',
        'base_hourly_rate',
        'base_daily_rate',
    ];


    protected $casts = [
        'period_amount'     => 'decimal:2',
        'annual_salary'     => 'decimal:2',
        'base_hourly_rate'  => 'decimal:2',
        'base_daily_rate'   => 'decimal:2',
    ];

}

