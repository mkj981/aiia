<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayOption extends Model
{
    use HasActivityLog;

    public const WORKING_PATTERN_DEFAULT_STANDARD_WEEK = 'default_standard_week';

    protected $fillable = [
        'employee_id',
        'pay_schedule_id',
        'pay_basis_id',
        'working_pattern',
        'period_amount',
        'annual_salary',
        'hourly_rate',
        'hours_in_period',
        'day_rate',
        'days_in_period',
        'period_total',
        'minimum_wage',
        'pay_code',
        'pro_rata_adjustment',
        'base_hourly_rate',
        'base_daily_rate',
    ];

    protected function casts(): array
    {
        return [
            'minimum_wage' => 'boolean',
            'period_amount' => 'decimal:2',
            'annual_salary' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'hours_in_period' => 'decimal:2',
            'day_rate' => 'decimal:2',
            'days_in_period' => 'decimal:2',
            'period_total' => 'decimal:2',
            'base_hourly_rate' => 'decimal:2',
            'base_daily_rate' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function paySchedule(): BelongsTo
    {
        return $this->belongsTo(PaySchedule::class, 'pay_schedule_id');
    }

    public function payBasis(): BelongsTo
    {
        return $this->belongsTo(PayBasis::class, 'pay_basis_id');
    }

    /**
     * @return array<string, string>
     */
    public static function workingPatternOptions(): array
    {
        return [
            self::WORKING_PATTERN_DEFAULT_STANDARD_WEEK => 'Default (Standard Working Week)',
        ];
    }

    public static function labelForWorkingPattern(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return self::workingPatternOptions()[$value] ?? $value;
    }
}
