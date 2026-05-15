<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeAdditionDeductions\Pages;

use App\Filament\Clusters\PayOptions\Resources\EmployeeAdditionDeductions\EmployeeAdditionDeductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeAdditionDeductions extends ManageRecords
{
    protected static string $resource = EmployeeAdditionDeductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Create Line'),
        ];
    }
}
