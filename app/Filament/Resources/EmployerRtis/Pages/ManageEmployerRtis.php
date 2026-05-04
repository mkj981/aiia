<?php

namespace App\Filament\Resources\EmployerRtis\Pages;

use App\Filament\Resources\EmployerRtis\EmployerRtiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployerRtis extends ManageRecords
{
    protected static string $resource = EmployerRtiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
