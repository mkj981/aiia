<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'name',
        'logo',
        'payroll_start_year',
        'company_number',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'postcode',
        'country',
    ];

    /**
     * @return array<int, string> value => label for Filament selects
     */
    public static function payrollStartYearSelectOptions(): array
    {
        $currentYear = now()->year;
        return collect(range($currentYear, 2017))->mapWithKeys(function ($year) {
                return [
                    $year => $year . '/' . substr($year + 1, -2),
                ];
            })->toArray();
    }

    public function getCountryNameAttribute(): ?string
    {
        return config('general.COUNTRIES')[$this->country] ?? $this->country;
    }
}
