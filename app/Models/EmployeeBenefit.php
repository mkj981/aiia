<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeBenefit extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_id',
        'description',
        'tax_year',
        'declaration_type',
        'benefit_type_id',
    ];
}
