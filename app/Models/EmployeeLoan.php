<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLoan extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'issue_date',
        'reference',
        'pay_code_id',
        'pause_payments',
        'loan_amount',
        'previously_paid',
        'period_amount',
        'amount_repaid',
        'balance',
    ];


    protected $casts = [
        'issue_date'        => 'date',
        'pause_payments'    => 'boolean',

        'loan_amount'       => 'decimal:2',
        'previously_paid'   => 'decimal:2',
        'period_amount'     => 'decimal:2',
        'amount_repaid'     => 'decimal:2',
        'balance'           => 'decimal:2',
    ];
}
