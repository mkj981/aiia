<?php

namespace App\Filament\Exports;

use App\Models\EmployerDefaultPayOption;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class EmployerRegularPayExporter extends Exporter
{
    protected static ?string $model = EmployerDefaultPayOption::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('employer.name'),
            ExportColumn::make('paySchedule.name'),
            ExportColumn::make('payBasis.name'),
            ExportColumn::make('pay_code'),
            ExportColumn::make('pro_rata_adjustment'),
            ExportColumn::make('period_amount'),
            ExportColumn::make('annual_salary'),
            ExportColumn::make('allow_negative_net_pay'),
            ExportColumn::make('automatically_calculate_back_pay_for_new_starters'),
            ExportColumn::make('enable_paycode_validation'),
            ExportColumn::make('calculate_effective_date_salary_changes'),
            ExportColumn::make('group_paylines_on_payslip'),
            ExportColumn::make('sort_payroll_numbers_alpha_numerically'),
            ExportColumn::make('contracted_weeks'),
            ExportColumn::make('full_time_contracted_weeks'),
            ExportColumn::make('full_time_contracted_hours_per_week'),
            ExportColumn::make('base_hourly_rate'),
            ExportColumn::make('base_daily_rate'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer regular pay export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    /**
     * @param Builder $query
     * @return Builder
     * When exporting, load these relationships in advance. To Avoid the n+1 Problem
     */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with([
            'employer',
            'paySchedule',
            'payBasis',
        ]);
    }
}
