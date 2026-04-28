<?php

namespace App\Filament\Resources\EmployerBankAccounts\Pages;

use App\Filament\Resources\EmployerBankAccounts\EmployerBankAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployerBankAccounts extends ManageRecords
{
    protected static string $resource = EmployerBankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth('6xl'),
        ];
    }
}
