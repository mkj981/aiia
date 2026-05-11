<?php

namespace App\Filament\Resources\EmployeePensions;

use App\Filament\Resources\EmployeePensions\Pages\ManageEmployeePensions;
use App\Models\EmployeePension;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeePensionResource extends Resource
{
    protected static ?string $model = EmployeePension::class;

    protected static ?string $recordTitleAttribute = 'uk_worker';

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Pension';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('employee_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make('Pension Details')->schema([

                    Select::make('uk_worker')->options(config('general.employee_pension')),
                    Toggle::make('exempt_from_ae')->label('Exempt From AE'),
                    Textarea::make('note')->columnSpanFull(),

                ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('uk_worker')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),

                TextColumn::make('uk_worker')
                    ->searchable(),
                IconColumn::make('exempt_from_ae')
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeePensions::route('/'),
        ];
    }
}
