<?php

namespace App\Filament\Resources\Employers;

use App\Filament\Resources\Employers\Pages\ManageEmployers;
use App\Models\Employer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use App\Filament\Exports\EmployerExporter;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class EmployerResource extends Resource
{
    protected static ?string $model = Employer::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employers';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Name & Address';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Employer Details')
                    ->description('Basic employer information')
                    ->schema([
                        TextInput::make('name')->label('Employer name')->maxLength(255)->columnSpanFull(),
                        Select::make('payroll_start_year')->label('Payroll start year')->options(Employer::payrollStartYearSelectOptions())->searchable(),
                        TextInput::make('company_number')->label('Company number')->maxLength(255),

                        TextInput::make('id')
                            ->label('Employer Id')
                            ->readOnly()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->extraInputAttributes([
                                'style' => 'background-color: #f3f4f6 !important; color: #4b5563 !important;',
                            ])
                            ->suffixAction(
                                Action::make('copyEmployerId')
                                    ->icon('heroicon-o-clipboard')
                                    ->tooltip('Copy Employer ID')
                                    ->extraAttributes([
                                        'x-on:click' => 'navigator.clipboard.writeText($wire.$get("data.id"));new FilamentNotification().title("Employer ID copied").success().send();',
                                    ])
                            ),


                    ])->columns(2)->columnSpan(2),

                Section::make('Logo')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Employer Logo')->image()->imageEditor()
                            ->acceptedFileTypes(config('general.ACCEPT_IMAGE_TYPE'))
                            ->directory('employer-logos')
                            ->disk('local')
                            ->visibility('private')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(1),

                Section::make('Address')
                    ->schema([
                        TextInput::make('address_line_1')->label('Address line 1'),
                        TextInput::make('address_line_2')->label('Address line 2'),
                        TextInput::make('address_line_3')->label('Address line 3'),
                        TextInput::make('address_line_4')->label('Address line 4'),
                        TextInput::make('postcode')->label('Postcode'),
                        Select::make('country')->options(config('general.COUNTRIES'))->searchable(),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Employer')
            ->columns([
                ImageColumn::make('logo')->disk('local')->square(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('payroll_start_year')->numeric()->sortable(),
                TextColumn::make('company_number')->searchable(),
                TextColumn::make('postcode')->searchable(),
                TextColumn::make('country_name'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
                        ->exporter(EmployerExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployers::route('/'),
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
