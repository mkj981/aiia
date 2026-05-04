<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeLoanDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_loan_id',
        'file_path',
        'file_name',
    ];
}
