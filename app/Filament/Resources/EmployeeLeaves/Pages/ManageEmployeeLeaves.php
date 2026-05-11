<?php

namespace App\Filament\Resources\EmployeeLeaves\Pages;

use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use App\Models\EmployeeLeave;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ManageEmployeeLeaves extends ManageRecords
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Leave')
                ->using(function (array $data, HasActions&HasSchemas $livewire): Model {
                    $paths = Arr::wrap($data['document_uploads'] ?? []);
                    $names = Arr::wrap($data['document_upload_file_names'] ?? []);
                    Arr::forget($data, ['document_uploads', 'document_upload_file_names']);

                    if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                        $record = $translatableContentDriver->makeRecord(EmployeeLeave::class, $data);
                    } else {
                        $record = new EmployeeLeave;
                        $record->fill($data);
                    }

                    $record->save();

                    if ($record instanceof EmployeeLeave) {
                        $record->syncDocumentsFromUploadState($paths, $names);
                    }

                    return $record;
                }),
        ];
    }
}
