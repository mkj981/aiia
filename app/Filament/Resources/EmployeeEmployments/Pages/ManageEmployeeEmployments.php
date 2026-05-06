<?php

namespace App\Filament\Resources\EmployeeEmployments\Pages;

use App\Filament\Resources\EmployeeEmployments\EmployeeEmploymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeEmployments extends ManageRecords
{
    protected static string $resource = EmployeeEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Employment'),
        ];
    }
}
