<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoanDocument extends Model
{
    protected $fillable = [
        'employee_loan_id',
        'file_path',
        'file_name',
    ];
}
