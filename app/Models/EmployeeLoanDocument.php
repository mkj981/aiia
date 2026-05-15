<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoanDocument extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_loan_id',
        'file_path',
        'file_name',
    ];

    public function employeeLoan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class);
    }
}
