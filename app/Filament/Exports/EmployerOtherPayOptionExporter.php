<?php

namespace App\Filament\Exports;

use App\Models\EmployerOtherPayOptions;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class EmployerOtherPayOptionExporter extends Exporter
{
    protected static ?string $model = EmployerOtherPayOptions::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('employer.name'),
            ExportColumn::make('student_loan_plan'),
            ExportColumn::make('postgrad_loan'),
            ExportColumn::make('hours_normally_worked_band'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('vehicle_type'),
            ExportColumn::make('withhold_tax_refund_if_gross_pay_zero'),
            ExportColumn::make('off_payroll_worker'),
            ExportColumn::make('irregular_payment_pattern'),
            ExportColumn::make('non_individual'),
            ExportColumn::make('exclude_from_rti_submissions'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employer other pay option export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
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
        ]);
    }
}
