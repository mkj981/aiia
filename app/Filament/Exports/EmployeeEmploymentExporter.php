<?php

namespace App\Filament\Exports;

use App\Models\EmployeeEmployment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeEmploymentExporter extends Exporter
{
    protected static ?string $model = EmployeeEmployment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee.full_name')
                ->label('Employee'),
            ExportColumn::make('job_title'),
            ExportColumn::make('postcode'),
            ExportColumn::make('start_date'),
            ExportColumn::make('continuous_start_date'),
            ExportColumn::make('payroll_code'),
            ExportColumn::make('declaration'),
            ExportColumn::make('change_of_payroll_id'),
            ExportColumn::make('previous_payroll_code'),
            ExportColumn::make('exclude_from_pay_runs'),
            ExportColumn::make('pension_payroll_start_date'),
            ExportColumn::make('annual_pension_amount'),
            ExportColumn::make('works_in_freeport'),
            ExportColumn::make('works_in_investment_zone'),
            ExportColumn::make('leave_date'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee employment export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
