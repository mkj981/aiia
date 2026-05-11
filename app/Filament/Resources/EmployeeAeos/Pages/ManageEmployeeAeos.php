<?php

namespace App\Filament\Resources\EmployeeAeos\Pages;

use App\Filament\Resources\EmployeeAeos\EmployeeAeosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeAeos extends ManageRecords
{
    protected static string $resource = EmployeeAeosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
