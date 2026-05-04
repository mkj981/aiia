<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerOtherPayOptions\Pages;

use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerOtherPayOptions\EmployerOtherPayOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployerOtherPayOptions extends ManageRecords
{
    protected static string $resource = EmployerOtherPayOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Other'),
        ];
    }
}
