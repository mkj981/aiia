<?php

namespace App\Filament\Exports;

use App\Models\EmployeeBankAccount;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeBankAccountExporter extends Exporter
{
    protected static ?string $model = EmployeeBankAccount::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee.full_name')
                ->label('Employee'),
            ExportColumn::make('bank_name'),
            ExportColumn::make('bank_branch'),
            ExportColumn::make('bank_reference'),
            ExportColumn::make('account_name'),
            ExportColumn::make('account_number'),
            ExportColumn::make('iban'),
            ExportColumn::make('sort_code'),
            ExportColumn::make('building_society_reference'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee bank account export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
