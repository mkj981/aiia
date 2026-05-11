<?php

namespace App\Filament\Resources\EmployeeAeos;

use App\Filament\Exports\EmployeeAeoExporter;
use App\Filament\Resources\EmployeeAeos\Pages\ManageEmployeeAeos;
use App\Models\AeoType;
use App\Models\EmployeeAeo;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class EmployeeAeosResource extends Resource
{
    protected static ?string $model = EmployeeAeo::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'aeo_type_id';

    protected static ?string $navigationLabel = 'Aeo';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('employee_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make('AEO Details')->schema([

                    Select::make('aeo_type_id')
                        ->label('AEO Types')
                        ->relationship(
                            'aeoTypes',
                            'name',
                            fn (Builder $query): Builder => $query->active()->orderBy('name'),
                        )
                        ->searchable(['name', 'description'])
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn (AeoType $record): string => static::aeotypeOptionLabel($record))
                        ->allowHtml()
                        ->live()->columnSpanFull(),

                    TextInput::make('reference'),
                    DatePicker::make('issue_date'),
                    Toggle::make('apply_admin_fee')
                        ->label(new HtmlString('
                                        Apply Admin Fee
                                        <span style="font-size:12px; color:#6b7280;">
                                            Charge £1
                                        </span>
                                    '))
                        ->columnSpanFull(),

                ])->columns(2)->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),

                TextColumn::make('aeoTypes.name')
                    ->label('AEO Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reference')
                    ->searchable(),

                TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),

                IconColumn::make('apply_admin_fee')
                    ->label('Admin Fee')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
                        ->exporter(EmployeeAeoExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeAeos::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    private static function aeotypeOptionLabel(AeoType $record): string
    {
        $description = e($record->description ?? '');

        return '<span>'.e($record->name).'</span> <span style="color: #7f8c8d; font-size: 12px">'.$description.'</span>';
    }
}
