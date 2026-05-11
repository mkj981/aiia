<?php

namespace App\Filament\Resources\EmployeeBankAccounts;

use App\Filament\Exports\EmployeeBankAccountExporter;
use App\Filament\Resources\EmployeeBankAccounts\Pages\ManageEmployeeBankAccounts;
use App\Models\EmployeeBankAccount;
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
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeBankAccountResource extends Resource
{
    protected static ?string $model = EmployeeBankAccount::class;

    protected static ?string $recordTitleAttribute = 'bank_name';

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Bank Account';

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

                Section::make('Bank Account Details')->schema([

                    TextInput::make('bank_name'),
                    TextInput::make('bank_branch'),
                    TextInput::make('bank_reference'),
                    TextInput::make('account_name'),
                    TextInput::make('account_number'),
                    TextInput::make('sort_code'),
                    TextInput::make('building_society_reference'),
                ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bank_name')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),
                TextColumn::make('bank_name')
                    ->searchable(),
                TextColumn::make('bank_branch')
                    ->searchable(),
                TextColumn::make('bank_reference')
                    ->searchable(),
                TextColumn::make('account_name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->searchable(),
                TextColumn::make('sort_code')
                    ->searchable(),
                TextColumn::make('building_society_reference')
                    ->searchable(),
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
                        ->exporter(EmployeeBankAccountExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeBankAccounts::route('/'),
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
