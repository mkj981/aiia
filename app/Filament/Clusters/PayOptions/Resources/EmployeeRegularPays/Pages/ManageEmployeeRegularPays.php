<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeRegularPays\Pages;

use App\Filament\Clusters\PayOptions\Resources\EmployeeRegularPays\EmployeeRegularPayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageEmployeeRegularPays extends ManageRecords
{
    protected static string $resource = EmployeeRegularPayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth(Width::SixExtraLarge)->label('New Regular Pay'),
        ];
    }
}
