<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBenefit extends Model
{
    protected $fillable = [
        'employee_id',
        'description',
        'tax_year',
        'declaration_type',
        'benefit_type_id',
    ];
}
