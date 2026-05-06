<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeave extends Model
{
    use HasActivityLog, SoftDeletes;

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
