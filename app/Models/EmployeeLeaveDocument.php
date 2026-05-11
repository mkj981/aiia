<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_leave_id',
        'file_path',
        'file_name',
    ];

    /**
     * @return BelongsTo<EmployeeLeave, $this>
     */
    public function employeeLeave(): BelongsTo
    {
        return $this->belongsTo(EmployeeLeave::class);
    }
}
