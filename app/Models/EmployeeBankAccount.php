<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBankAccount extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'employee_id',

        'bank_name',
        'bank_branch',
        'bank_reference',

        'account_name',
        'account_number',
        'sort_code',

        'building_society_reference',
    ];

    protected $casts = [
        'account_number' => 'encrypted',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
