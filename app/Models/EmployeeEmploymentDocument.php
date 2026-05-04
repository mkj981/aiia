<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class EmployeeEmploymentDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_employment_id',
        'document_type',
        'expiry_date',
        'reference',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];
}
