<?php

namespace App\Filament\Exports;

use App\Models\EmployerHmrc;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployerHmrcExporter extends Exporter
{
    protected static ?string $model = EmployerHmrc::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employer.name'),
            ExportColumn::make('paye_office_number'),
            ExportColumn::make('paye_reference'),
            ExportColumn::make('accounts_office_reference'),
            ExportColumn::make('econ_number'),
            ExportColumn::make('utr'),
            ExportColumn::make('corporation_tax_reference'),
            ExportColumn::make('payment_schedule'),
            ExportColumn::make('carry_forward_unpaid_liabilities'),
            ExportColumn::make('payment_date_type'),
            ExportColumn::make('payment_day_of_month'),
            ExportColumn::make('qualifies_for_small_employers_relief'),
            ExportColumn::make('eligible_for_employment_allowance'),
            ExportColumn::make('employment_allowance_max_claim'),
            ExportColumn::make('include_employment_allowance_on_monthly_journal'),
            ExportColumn::make('required_to_pay_apprenticeship_levy'),
            ExportColumn::make('apprenticeship_levy_allowance'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer hmrc export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
