<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employer_id'),
            ExportColumn::make('photo_path'),
            ExportColumn::make('title'),
            ExportColumn::make('first_name'),
            ExportColumn::make('middle_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('age'),
            ExportColumn::make('gender'),
            ExportColumn::make('marital_status'),
            ExportColumn::make('email'),
            ExportColumn::make('alternative_email'),
            ExportColumn::make('telephone'),
            ExportColumn::make('mobile'),
            ExportColumn::make('passport_number'),
            ExportColumn::make('ni_number'),
            ExportColumn::make('previous_surname'),
            ExportColumn::make('address_line_1'),
            ExportColumn::make('address_line_2'),
            ExportColumn::make('address_line_3'),
            ExportColumn::make('address_line_4'),
            ExportColumn::make('postcode'),
            ExportColumn::make('country'),
            ExportColumn::make('has_partner'),
            ExportColumn::make('partner_first_name'),
            ExportColumn::make('partner_initials'),
            ExportColumn::make('partner_last_name'),
            ExportColumn::make('partner_ni_number'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
