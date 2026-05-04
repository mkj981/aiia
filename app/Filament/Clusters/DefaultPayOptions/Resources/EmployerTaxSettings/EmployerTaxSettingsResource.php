<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerTaxSettings;

use App\Filament\Clusters\DefaultPayOptions\DefaultPayOptionsCluster;
use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerTaxSettings\Pages\ManageEmployerTaxSettings;
use App\Filament\Exports\EmployerTaxSettingExporter;
use App\Models\EmployerTaxSetting;
use App\Models\Ni;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;


class EmployerTaxSettingsResource extends Resource
{
    protected static ?string $model = EmployerTaxSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Tax & NI';

    protected static ?string $cluster = DefaultPayOptionsCluster::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('employer_id')->default(fn () => auth()->id()),
                Section::make('Tax & NI')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('tax_code')
                            ->label('Tax Code')
                            ->default('Default')
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(50),

                        Select::make('ni_id')
                            ->label('NI')
                            ->relationship(
                                'ni',
                                'code',
                                fn (Builder $query) => $query->active()
                            )
                            ->getOptionLabelFromRecordUsing(fn (Ni $record): string => static::niOptionLabel($record))
                            ->searchable()
                            ->allowHtml()
                            ->preload()
                            ->nullable(),


                        Toggle::make('week1_month1')
                            ->label('Week 1 / Month 1')
                            ->columnSpanFull(),


                        Toggle::make('ni_secondary_class_nics_not_payable')
                            ->label('NI Secondary Class NICs Not Payable')
                            ->columnSpanFull(),

                        Toggle::make('enable_foreign_tax_credit')
                            ->label('Enable Foreign Tax Credit')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tax_code')
            ->columns([
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tax_code')
                    ->label('Tax Code')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('week1_month1')
                    ->label('Week 1 / Month 1')
                    ->boolean(),

                TextColumn::make('ni.code')
                    ->label('NI')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('ni_secondary_class_nics_not_payable')
                    ->label('NI Secondary Not Payable')
                    ->boolean(),

                IconColumn::make('enable_foreign_tax_credit')
                    ->label('Foreign Tax Credit')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
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

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployerTaxSettingExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employer', 'ni']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerTaxSettings::route('/'),
        ];
    }

    public static function niOptionLabel(Ni $record): string
    {
        $description = e($record->description ?? '');
        return '<span>'.e($record->code).'</span> <span style="color: #7f8c8d; font-size: 12px">'.$description.'</span>';
    }
}
