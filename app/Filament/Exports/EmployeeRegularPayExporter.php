<?php

namespace App\Filament\Exports;

use App\Models\EmployeePayOption;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class EmployeeRegularPayExporter extends Exporter
{
    protected static ?string $model = EmployeePayOption::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('employee.full_name'),
            ExportColumn::make('paySchedule.name'),
            ExportColumn::make('payBasis.name'),
            ExportColumn::make('working_pattern')
                ->label('Working Pattern')
                ->formatStateUsing(fn (?string $state): string => EmployeePayOption::labelForWorkingPattern($state)),
            ExportColumn::make('pay_code'),
            ExportColumn::make('pro_rata_adjustment'),
            ExportColumn::make('period_amount'),
            ExportColumn::make('annual_salary'),
            ExportColumn::make('hourly_rate'),
            ExportColumn::make('hours_in_period'),
            ExportColumn::make('day_rate'),
            ExportColumn::make('days_in_period'),
            ExportColumn::make('period_total'),
            ExportColumn::make('minimum_wage'),
            ExportColumn::make('base_hourly_rate'),
            ExportColumn::make('base_daily_rate'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee regular pay export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    /**
     * When exporting, load these relationships in advance to avoid the N+1 problem.
     */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with([
            'employee',
            'paySchedule',
            'payBasis',
        ]);
    }
}
