<?php

namespace App\Filament\Resources\EmployeePensions\Pages;

use App\Filament\Resources\EmployeePensions\EmployeePensionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeePensions extends ManageRecords
{
    protected static string $resource = EmployeePensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
