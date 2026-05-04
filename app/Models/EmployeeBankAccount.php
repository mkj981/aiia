<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeBankAccount extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_id',

        'bank_name',
        'bank_branch',
        'bank_reference',

        'account_name',
        'account_number',
        'sort_code',

        'building_society_reference',
    ];
}
