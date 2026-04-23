<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePension extends Model
{
    protected $fillable = [
        'employee_id',
        'uk_worker',
        'exempt_from_ae',
        'note',
    ];

    protected $casts = [
        'exempt_from_ae' => 'boolean',
    ];
}
