<?php

namespace App\Filament\Exports;

use App\Models\Rti;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class RtiExporter extends Exporter
{
    protected static ?string $model = Rti::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employer.name'),
            ExportColumn::make('sender_type_id'),
            ExportColumn::make('govt_gateway_id'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('auto_submit_fps_after_finalising_pay_run'),
            ExportColumn::make('include_employees_with_no_payment_on_fps'),
            ExportColumn::make('test_mode'),
            ExportColumn::make('use_test_gateway'),
            ExportColumn::make('allow_linked_eps'),
            ExportColumn::make('compress_fps'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your rti export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
