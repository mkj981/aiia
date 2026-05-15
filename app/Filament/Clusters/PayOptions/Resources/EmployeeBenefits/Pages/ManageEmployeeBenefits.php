<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeBenefits\Pages;

use App\Filament\Clusters\PayOptions\Resources\EmployeeBenefits\EmployeeBenefitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageEmployeeBenefits extends ManageRecords
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Benefit')
                ->modalHeading('Add New Benefit')
                ->modalSubmitActionLabel('Create Benefit')
                ->modalWidth(Width::FourExtraLarge)
                ->createAnother(false),
        ];
    }
}
