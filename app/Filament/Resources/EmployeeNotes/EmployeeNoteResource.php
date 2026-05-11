<?php

namespace App\Filament\Resources\EmployeeNotes;

use App\Filament\Exports\EmployeeNoteExporter;
use App\Filament\Resources\EmployeeNotes\Pages\ManageEmployeeNotes;
use App\Filament\Support\EmployeeRecordDocumentUploads;
use App\Models\EmployeeNote;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeNoteResource extends Resource
{
    protected static ?string $model = EmployeeNote::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $recordTitleAttribute = 'note';

    protected static ?string $navigationLabel = 'Notes';

    protected static ?int $navigationSort = 13;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('employee_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make('Note Details')->schema([

                    Select::make('employee_note_type_id')
                        ->label('Note Type')
                        ->relationship(
                            'noteType',
                            'name',
                            fn (Builder $query): Builder => $query->active()->orderBy('id'),
                        )
                        ->searchable()
                        ->preload()->columnSpanFull(),

                    Textarea::make('note')->columnSpanFull(),

                    EmployeeRecordDocumentUploads::fileUpload('employee-note-documents'),
                ])->columns(2)->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(EmployeeRecordDocumentUploads::eagerLoadDocuments())
            ->recordTitleAttribute('note')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee'),
                TextColumn::make('noteType.name')
                    ->label('Note Type'),

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
                    ->mutateRecordDataUsing(EmployeeRecordDocumentUploads::mutateEditFormRecordDataUsing(EmployeeNote::class))
                    ->using(EmployeeRecordDocumentUploads::editSaveUsing(EmployeeNote::class)),
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
                        ->exporter(EmployeeNoteExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeNotes::route('/'),
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
