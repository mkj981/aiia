<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAdditionDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'pay_code_id',
        'fixed_period_amount',
        'gross_up_target_net',
        'pro_rata_adjustment',
        'description',
        'effective_from',
        'effective_to',
    ];


    protected $casts = [
        'fixed_period_amount'       => 'decimal:2',
        'gross_up_target_net'       => 'boolean',
        'effective_from'            => 'date',
        'effective_to'              => 'date',
    ];
}
