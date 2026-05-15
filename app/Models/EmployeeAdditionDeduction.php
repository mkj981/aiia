<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdditionDeduction extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'employee_id',
        'pay_code_id',
        'fixed_period_amount',
        'fixed_annual_amount',
        'full_time_annual_value',
        'quantity',
        'rate',
        'gross_up_target_net',
        'pro_rata_adjustment',
        'description',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'fixed_period_amount' => 'decimal:2',
            'fixed_annual_amount' => 'decimal:2',
            'full_time_annual_value' => 'decimal:2',
            'quantity' => 'decimal:2',
            'rate' => 'decimal:2',
            'gross_up_target_net' => 'boolean',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (EmployeeAdditionDeduction $line): void {
            $line->normalizeAttributesForPayCode();
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payCode(): BelongsTo
    {
        return $this->belongsTo(PayCode::class);
    }

    public function normalizeAttributesForPayCode(): void
    {
        $name = $this->relationLoaded('payCode')
            ? $this->payCode?->name
            : PayCode::query()->whereKey($this->pay_code_id)->value('name');

        if (! is_string($name) || $name === '') {
            return;
        }

        /** @var array<string, string> $kinds */
        $kinds = config('general.employee_addition_deduction_line_kinds', []);
        $kind = $kinds[$name] ?? null;

        match ($kind) {
            'fixed_period' => $this->applyFixedPeriodStorageShape(),
            'fixed_annual' => $this->applyFixedAnnualStorageShape(),
            'hourly', 'daily' => $this->applyQuantityRateStorageShape(),
            default => null,
        };
    }

    private function applyFixedPeriodStorageShape(): void
    {
        $this->fixed_annual_amount = null;
        $this->full_time_annual_value = null;
        $this->quantity = null;
        $this->rate = null;
    }

    private function applyFixedAnnualStorageShape(): void
    {
        $this->fixed_period_amount = null;
        $this->quantity = null;
        $this->rate = null;
    }

    private function applyQuantityRateStorageShape(): void
    {
        $this->fixed_annual_amount = null;
        $this->full_time_annual_value = null;
        $this->gross_up_target_net = false;
        $this->pro_rata_adjustment = null;

        $quantity = (float) ($this->quantity ?? 0);
        $rate = (float) ($this->rate ?? 0);
        $this->fixed_period_amount = round($quantity * $rate, 2);
    }
}
