<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePension extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_id',
        'uk_worker',
        'exempt_from_ae',
        'note',
    ];

    protected $casts = [
        'exempt_from_ae' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
