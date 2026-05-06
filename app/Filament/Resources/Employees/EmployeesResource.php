<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Exports\EmployeeExporter;
use App\Filament\Resources\Employees\Pages\ManageEmployees;
use App\Models\Employee;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class EmployeesResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $navigationLabel = 'Basic Details';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Hidden::make('employer_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make('Name')
                    ->schema([
                        TextInput::make('title')->maxLength(20),

                        TextInput::make('first_name')
                            ->maxLength(100),

                        TextInput::make('middle_name')
                            ->maxLength(100),

                        TextInput::make('last_name')
                            ->maxLength(100),

                        DatePicker::make('date_of_birth')
                            ->live()
                            ->afterStateHydrated(function (mixed $state, Set $set): void {
                                $set('age', blank($state) ? null : (string) Carbon::parse($state)->age);
                            })
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                $set('age', blank($state) ? null : (string) Carbon::parse($state)->age);
                            }),

                        TextInput::make('age')
                            ->readOnly()
                            ->dehydrated()
                            ->maxLength(20)
                            ->extraInputAttributes([
                                'style' => 'background-color: #f3f4f6 !important; color: #4b5563 !important;',
                            ]),

                        Select::make('gender')
                            ->options(config('general.gender')),

                        Select::make('marital_status')
                            ->options(config('general.marital_status')),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Photo')
                    ->schema([
                        FileUpload::make('photo_path')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(config('general.ACCEPT_IMAGE_TYPE'))
                            ->directory('employee-photos')
                            ->disk('local')
                            ->visibility('private')
                            ->maxSize(2048)
                            ->panelLayout('integrated')
                            ->extraAttributes([
                                'class' => '[&_.filepond--root]:overflow-visible',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(1),

                // Contact
                Section::make('Contact')
                    ->schema([

                        TextInput::make('email')
                            ->email()
                            ->maxLength(150),

                        TextInput::make('alternative_email')
                            ->email()
                            ->maxLength(150),

                        TextInput::make('telephone')
                            ->tel()
                            ->maxLength(30),

                        TextInput::make('mobile')
                            ->tel()
                            ->maxLength(30),

                    ])->columns(2)->columnSpanFull(),

                // Identity
                Section::make('Identity')
                    ->schema([
                        TextInput::make('passport_number')
                            ->maxLength(50),

                        TextInput::make('ni_number')
                            ->maxLength(20),

                        TextInput::make('previous_surname')
                            ->maxLength(100),
                    ])->columns(3)->columnSpanFull(),

                // Address
                Section::make('Address')
                    ->schema([
                        TextInput::make('address_line_1')->maxLength(150),
                        TextInput::make('address_line_2')->maxLength(150),
                        TextInput::make('address_line_3')->maxLength(150),
                        TextInput::make('address_line_4')->maxLength(150),
                        TextInput::make('postcode')->maxLength(20),
                        TextInput::make('country')->maxLength(100),
                    ])->columns(3)->columnSpanFull(),

                // Partner
                Section::make('Partner')
                    ->schema([
                        Toggle::make('has_partner')
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                if (! (bool) $state) {
                                    $set('partner_first_name', null);
                                    $set('partner_initials', null);
                                    $set('partner_last_name', null);
                                    $set('partner_ni_number', null);
                                }
                            }),

                        TextInput::make('partner_first_name')
                            ->maxLength(100)
                            ->visible(fn (Get $get): bool => (bool) $get('has_partner')),

                        TextInput::make('partner_initials')
                            ->maxLength(20)
                            ->visible(fn (Get $get): bool => (bool) $get('has_partner')),

                        TextInput::make('partner_last_name')
                            ->maxLength(100)
                            ->visible(fn (Get $get): bool => (bool) $get('has_partner')),

                        TextInput::make('partner_ni_number')
                            ->maxLength(20)
                            ->visible(fn (Get $get): bool => (bool) $get('has_partner')),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([

                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->circular()
                    ->height(50)
                    ->width(50),

                TextColumn::make('first_name')->searchable()->sortable(),

                TextColumn::make('middle_name')->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_name')->searchable()->sortable(),

                TextColumn::make('title')->searchable(),

                TextColumn::make('age')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')->badge(),
                TextColumn::make('marital_status')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->searchable(),
                TextColumn::make('alternative_email')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mobile')->searchable(),
                TextColumn::make('telephone')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('passport_number')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ni_number')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('previous_surname')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_line_1')->searchable(),
                TextColumn::make('address_line_2')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_line_3')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address_line_4')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('postcode')->searchable(),
                TextColumn::make('country')->searchable(),
                TextColumn::make('has_partner')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextColumn::make('partner_first_name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('partner_initials')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('partner_last_name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('partner_ni_number')->toggleable(isToggledHiddenByDefault: true),

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
                EditAction::make()->modalWidth(Width::SixExtraLarge),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployeeExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployees::route('/'),
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
