<?php

namespace App\Filament\Exports;

use App\Models\EmployerTaxSetting;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployerTaxSettingExporter extends Exporter
{
    protected static ?string $model = EmployerTaxSetting::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employer.name'),
            ExportColumn::make('tax_code'),
            ExportColumn::make('week1_month1'),
            ExportColumn::make('ni.code'),
            ExportColumn::make('ni_secondary_class_nics_not_payable'),
            ExportColumn::make('enable_foreign_tax_credit'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer tax setting export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
