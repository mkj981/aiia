<?php

namespace App\Filament\Resources\EmployeeLeaves\Pages;

use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use App\Filament\Support\EmployeeRecordDocumentUploads;
use App\Models\EmployeeLeave;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeLeaves extends ManageRecords
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Leave')
                ->using(EmployeeRecordDocumentUploads::createUsing(EmployeeLeave::class)),
        ];
    }
}
