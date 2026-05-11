<?php

namespace App\Filament\Support;

use Closure;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

final class EmployeeRecordDocumentUploads
{
    /**
     * @var list<string>
     */
    public const ACCEPTED_FILE_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public static function fileUpload(string $directory): FileUpload
    {
        $prefix = rtrim($directory, '/').'/';

        return FileUpload::make('document_uploads')
            ->label('Documents')
            ->multiple()
            ->storeFileNamesIn('document_upload_file_names')
            ->directory($directory)
            ->disk('local')
            ->visibility('private')
            ->downloadable()
            ->openable()
            ->maxSize(10240)
            ->maxFiles(20)
            ->acceptedFileTypes(self::ACCEPTED_FILE_TYPES)
            ->preventFilePathTampering(
                allowFilePathUsing: static function (string $file) use ($prefix): bool {
                    if (! str_starts_with($file, $prefix)) {
                        return false;
                    }

                    return Storage::disk('local')->exists($file);
                },
            )
            ->columnSpanFull();
    }

    /**
     * @return Closure(Builder): Builder
     */
    public static function eagerLoadDocuments(): Closure
    {
        return fn (Builder $query): Builder => $query->with(['documents']);
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function mutateEditFormRecordDataUsing(string $modelClass): Closure
    {
        return static function (array $data, Model $record) use ($modelClass): array {
            if (! $record instanceof $modelClass) {
                return $data;
            }

            $documents = $record->documents()->orderBy('id')->get();

            $data['document_uploads'] = $documents->pluck('file_path')->all();
            $data['document_upload_file_names'] = $documents->pluck('file_name')->map(fn (?string $name): string => (string) $name)->all();

            return $data;
        };
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function editSaveUsing(string $modelClass): Closure
    {
        return static function (array $data, HasActions&HasSchemas $livewire, Model $record) use ($modelClass): void {
            $paths = Arr::wrap($data['document_uploads'] ?? []);
            $names = Arr::wrap($data['document_upload_file_names'] ?? []);
            Arr::forget($data, ['document_uploads', 'document_upload_file_names']);

            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                $translatableContentDriver->updateRecord($record, $data);
            } else {
                $record->update($data);
            }

            if ($record instanceof $modelClass) {
                $record->syncDocumentsFromUploadState($paths, $names);
            }
        };
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function createUsing(string $modelClass): Closure
    {
        return static function (array $data, HasActions&HasSchemas $livewire) use ($modelClass): Model {
            $paths = Arr::wrap($data['document_uploads'] ?? []);
            $names = Arr::wrap($data['document_upload_file_names'] ?? []);
            Arr::forget($data, ['document_uploads', 'document_upload_file_names']);

            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                $record = $translatableContentDriver->makeRecord($modelClass, $data);
            } else {
                $record = new $modelClass;
                $record->fill($data);
            }

            $record->save();

            if ($record instanceof $modelClass) {
                $record->syncDocumentsFromUploadState($paths, $names);
            }

            return $record;
        };
    }
}
