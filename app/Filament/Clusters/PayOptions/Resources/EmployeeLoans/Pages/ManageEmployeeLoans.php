<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeLoans\Pages;

use App\Filament\Clusters\PayOptions\Resources\EmployeeLoans\EmployeeLoanResource;
use App\Filament\Support\EmployeeRecordDocumentUploads;
use App\Models\EmployeeLoan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageEmployeeLoans extends ManageRecords
{
    protected static string $resource = EmployeeLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Loan')
                ->modalHeading('Add Loan')
                ->modalSubmitActionLabel('Create Loan')
                ->modalWidth(Width::FourExtraLarge)
                ->createAnother(false)
                ->using(EmployeeRecordDocumentUploads::createUsing(EmployeeLoan::class)),
        ];
    }
}
