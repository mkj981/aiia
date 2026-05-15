<?php

namespace App\Filament\Exports;

use App\Models\EmployeeAdditionDeduction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeAdditionDeductionExporter extends Exporter
{
    protected static ?string $model = EmployeeAdditionDeduction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee.full_name')
                ->label('Employee'),
            ExportColumn::make('payCode.name'),
            ExportColumn::make('fixed_period_amount'),
            ExportColumn::make('gross_up_target_net'),
            ExportColumn::make('pro_rata_adjustment'),
            ExportColumn::make('description'),
            ExportColumn::make('effective_from'),
            ExportColumn::make('effective_to'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('fixed_annual_amount'),
            ExportColumn::make('full_time_annual_value'),
            ExportColumn::make('quantity'),
            ExportColumn::make('rate'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee addition deduction export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
