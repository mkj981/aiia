<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'date_from',
        'date_to',
    ];


    protected $casts = [
        'date_from'         => 'date',
        'date_to'           => 'date',
    ];
}
