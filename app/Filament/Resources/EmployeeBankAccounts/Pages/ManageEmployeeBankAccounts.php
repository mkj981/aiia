<?php

namespace App\Filament\Resources\EmployeeBankAccounts\Pages;

use App\Filament\Resources\EmployeeBankAccounts\EmployeeBankAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeBankAccounts extends ManageRecords
{
    protected static string $resource = EmployeeBankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
