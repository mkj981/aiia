<?php

namespace App\Filament\Exports;

use App\Models\Employer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployerExporter extends Exporter
{
    protected static ?string $model = Employer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('logo'),
            ExportColumn::make('payroll_start_year'),
            ExportColumn::make('company_number'),
            ExportColumn::make('address_line_1'),
            ExportColumn::make('address_line_2'),
            ExportColumn::make('address_line_3'),
            ExportColumn::make('address_line_4'),
            ExportColumn::make('postcode'),
            ExportColumn::make('country'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
