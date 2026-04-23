<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBankAccount extends Model
{
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
