<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerRegularPays\Pages;

use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerRegularPays\EmployerRegularPayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageEmployerRegularPays extends ManageRecords
{
    protected static string $resource = EmployerRegularPayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth(Width::SixExtraLarge)->label('New Regular Pay'),
        ];
    }
}
