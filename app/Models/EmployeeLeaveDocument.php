<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_leave_id',
        'file_path',
        'file_name',
    ];

}
