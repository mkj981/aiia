<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerTaxSettings\Pages;

use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerTaxSettings\EmployerTaxSettingsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployerTaxSettings extends ManageRecords
{
    protected static string $resource = EmployerTaxSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Tax & NI'),
        ];
    }
}
