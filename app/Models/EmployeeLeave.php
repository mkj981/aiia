<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasActivityLog;

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
