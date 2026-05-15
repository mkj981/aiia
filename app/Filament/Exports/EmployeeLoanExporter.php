<?php

namespace App\Filament\Exports;

use App\Models\EmployeeLoan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeLoanExporter extends Exporter
{
    protected static ?string $model = EmployeeLoan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee.full_name')
                ->label('Employee'),
            ExportColumn::make('issue_date'),
            ExportColumn::make('reference'),
            ExportColumn::make('payCode.name'),
            ExportColumn::make('pause_payments'),
            ExportColumn::make('loan_amount'),
            ExportColumn::make('previously_paid'),
            ExportColumn::make('period_amount'),
            ExportColumn::make('amount_repaid'),
            ExportColumn::make('balance'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee loan export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
