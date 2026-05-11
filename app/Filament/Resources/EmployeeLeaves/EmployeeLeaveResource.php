<?php

namespace App\Filament\Resources\EmployeeLeaves;

use App\Filament\Exports\EmployeeLeaveExporter;
use App\Filament\Resources\EmployeeLeaves\Pages\ManageEmployeeLeaves;
use App\Models\EmployeeLeave;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class EmployeeLeaveResource extends Resource
{
    protected static ?string $model = EmployeeLeave::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $recordTitleAttribute = 'leave_type_id';

    protected static ?string $navigationLabel = 'Leaves';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('employee_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make('Leave Details')->schema([

                    Select::make('leave_type_id')
                        ->label('Leave Type')
                        ->relationship(
                            'leaveType',
                            'name',
                            fn (Builder $query): Builder => $query->active()->orderBy('id'),
                        )
                        ->searchable()
                        ->preload()->columnSpanFull(),

                    DatePicker::make('date_from'),
                    DatePicker::make('date_to'),

                    FileUpload::make('document_uploads')
                        ->label('Documents')
                        ->multiple()
                        ->storeFileNamesIn('document_upload_file_names')
                        ->directory('employee-leave-documents')
                        ->disk('local')
                        ->visibility('private')
                        ->downloadable()
                        ->openable()
                        ->maxSize(10240)
                        ->maxFiles(20)
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->preventFilePathTampering(
                            allowFilePathUsing: function (string $file): bool {
                                if (! str_starts_with($file, 'employee-leave-documents/')) {
                                    return false;
                                }

                                return Storage::disk('local')->exists($file);
                            },
                        )
                        ->columnSpanFull(),

                ])->columns(2)->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['documents']))
            ->recordTitleAttribute('leave_type_id')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),
                TextColumn::make('leavetype.name')
                    ->label('Leave Type')
                    ->searchable('name'),
                TextColumn::make('date_from')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_to')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        if (! $record instanceof EmployeeLeave) {
                            return $data;
                        }

                        $documents = $record->documents()->orderBy('id')->get();

                        $data['document_uploads'] = $documents->pluck('file_path')->all();
                        $data['document_upload_file_names'] = $documents->pluck('file_name')->map(fn (?string $name): string => (string) $name)->all();

                        return $data;
                    })
                    ->using(function (array $data, HasActions&HasSchemas $livewire, Model $record): void {
                        $paths = Arr::wrap($data['document_uploads'] ?? []);
                        $names = Arr::wrap($data['document_upload_file_names'] ?? []);
                        Arr::forget($data, ['document_uploads', 'document_upload_file_names']);

                        if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                            $translatableContentDriver->updateRecord($record, $data);
                        } else {
                            $record->update($data);
                        }

                        if ($record instanceof EmployeeLeave) {
                            $record->syncDocumentsFromUploadState($paths, $names);
                        }
                    }),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployeeLeaveExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeLeaves::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
