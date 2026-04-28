<?php

namespace App\Filament\Exports;

use App\Models\EmployerBankAccount;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployerBankAccountExporter extends Exporter
{
    protected static ?string $model = EmployerBankAccount::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employer.name'),
            ExportColumn::make('bank_name'),
            ExportColumn::make('bank_branch'),
            ExportColumn::make('bank_reference'),
            ExportColumn::make('account_name'),
            ExportColumn::make('account_number'),
            ExportColumn::make('sort_code'),
            ExportColumn::make('building_society_reference'),
            ExportColumn::make('country_of_bank'),
            ExportColumn::make('iban'),
            ExportColumn::make('swift_bic'),
            ExportColumn::make('bankPaymentCsvFormat.name'),
            ExportColumn::make('bacs_sun'),
            ExportColumn::make('payment_reference_format'),
            ExportColumn::make('reject_invalid_employee_bank_details'),
            ExportColumn::make('include_attachment_of_earnings'),
            ExportColumn::make('include_deductions'),
            ExportColumn::make('include_hmrc_payment'),
            ExportColumn::make('include_pensions'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer bank account export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
