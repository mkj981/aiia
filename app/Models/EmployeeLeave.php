<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use App\Models\Concerns\SyncsDocumentsFromFilamentUploadState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeave extends Model
{
    use HasActivityLog, SoftDeletes, SyncsDocumentsFromFilamentUploadState;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeavesType::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return HasMany<EmployeeLeaveDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeLeaveDocument::class);
    }
}
