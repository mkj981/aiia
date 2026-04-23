<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveDocument extends Model
{

    protected $fillable = [
        'employee_leave_id',
        'file_path',
        'file_name',
    ];

}
