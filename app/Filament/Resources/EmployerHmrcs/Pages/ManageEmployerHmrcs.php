<?php

namespace App\Filament\Resources\EmployerHmrcs\Pages;

use App\Filament\Resources\EmployerHmrcs\EmployerHmrcResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployerHmrcs extends ManageRecords
{
    protected static string $resource = EmployerHmrcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
