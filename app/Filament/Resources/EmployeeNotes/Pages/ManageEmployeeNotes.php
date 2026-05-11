<?php

namespace App\Filament\Resources\EmployeeNotes\Pages;

use App\Filament\Resources\EmployeeNotes\EmployeeNoteResource;
use App\Filament\Support\EmployeeRecordDocumentUploads;
use App\Models\EmployeeNote;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeeNotes extends ManageRecords
{
    protected static string $resource = EmployeeNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Note')
                ->using(EmployeeRecordDocumentUploads::createUsing(EmployeeNote::class)),
        ];
    }
}
