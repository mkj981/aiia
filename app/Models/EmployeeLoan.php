<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use App\Models\Concerns\SyncsDocumentsFromFilamentUploadState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLoan extends Model
{
    use HasActivityLog, SoftDeletes, SyncsDocumentsFromFilamentUploadState;

    protected $fillable = [
        'employee_id',
        'issue_date',
        'reference',
        'pay_code_id',
        'pause_payments',
        'loan_amount',
        'previously_paid',
        'period_amount',
        'amount_repaid',
        'balance',
    ];

    protected static function booted(): void
    {
        static::saving(function (EmployeeLoan $loan): void {
            $loan->recalculateBalance();
        });
    }

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'pause_payments' => 'boolean',
            'loan_amount' => 'decimal:2',
            'previously_paid' => 'decimal:2',
            'period_amount' => 'decimal:2',
            'amount_repaid' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payCode(): BelongsTo
    {
        return $this->belongsTo(PayCode::class);
    }

    /**
     * @return HasMany<EmployeeLoanDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeLoanDocument::class);
    }

    public function recalculateBalance(): void
    {
        $this->balance = round(
            (float) $this->loan_amount - (float) $this->previously_paid - (float) $this->amount_repaid,
            2,
        );
    }
}
