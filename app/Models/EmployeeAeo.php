<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAeo extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'aeo_type_id',
        'issue_date',
        'reference',
        'apply_admin_fee',
    ];


    protected $casts = [
        'issue_date'            => 'date',
        'apply_admin_fee'       => 'boolean',
    ];
}
