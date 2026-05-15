<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeBenefits;

use App\Filament\Clusters\PayOptions\PayOptionsCluster;
use App\Filament\Clusters\PayOptions\Resources\EmployeeBenefits\Pages\ManageEmployeeBenefits;
use App\Filament\Exports\EmployeeBenefitExporter;
use App\Models\BenefitType;
use App\Models\EmployeeBenefit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeBenefitResource extends Resource
{
    protected static ?string $model = EmployeeBenefit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $cluster = PayOptionsCluster::class;

    protected static ?string $navigationLabel = 'Benefits';

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?string $modelLabel = 'benefit';

    protected static ?string $pluralModelLabel = 'benefits';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('employee_id')
                    ->default(fn (): ?int => auth()->id())
                    ->dehydrated()
                    ->required(fn (): bool => filled(auth()->id()))
                    ->columnSpanFull(),

                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('description')
                                    ->label('Description')
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Select::make('tax_year')
                                    ->label('Tax Year')
                                    ->options(fn (): array => config('general.employee_benefit_tax_years', []))
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('declaration_type')
                                    ->label('Declaration Type')
                                    ->options(fn (): array => static::declarationTypeSelectOptions())
                                    ->getOptionLabelUsing(fn (?string $value): ?string => $value ? static::declarationTypeOptionLabel($value) : null)
                                    ->searchable()
                                    ->native(false)
                                    ->allowHtml()
                                    ->columnSpan(1),
                            ])
                            ->columnSpanFull(),

                        Select::make('benefit_type_id')
                            ->label('Type')
                            ->relationship(
                                name: 'benefitType',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->active()->orderBy('code'),
                            )
                            ->getOptionLabelFromRecordUsing(fn (BenefitType $record): string => static::benefitTypeOptionLabel($record))
                            ->searchable()
                            ->preload()
                            ->allowHtml()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('tax_year')
                    ->label('Tax year')
                    ->sortable(),

                TextColumn::make('declaration_type')
                    ->label('Declaration')
                    ->formatStateUsing(function (?string $state): string {
                        if (blank($state)) {
                            return '-';
                        }

                        /** @var array<string, array{label: string, description: string}> $definitions */
                        $definitions = config('general.employee_benefit_declaration_types', []);
                        $label = $definitions[$state]['label'] ?? $state;

                        return (string) $label;
                    })
                    ->sortable(),

                TextColumn::make('benefitType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Benefit')
                    ->modalSubmitActionLabel('Save')
                    ->modalWidth(Width::FourExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployeeBenefitExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'benefitType']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeBenefits::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    private static function benefitTypeOptionLabel(BenefitType $record): string
    {
        $letter = e($record->section_letter);
        $name = e((string) $record->name);
        $description = e((string) ($record->description ?? ''));

        return '<span style="display:inline-block;min-width:1.35rem;text-align:center;font-weight:700;border:1px solid rgb(209 213 219);padding:0.1rem 0.35rem;margin-right:0.5rem;border-radius:0.125rem;font-size:0.75rem;">'
            .$letter.'</span>'
            .'<strong>'.$name.'</strong>'
            .' <span style="color:#7f8c8d;font-size:12px">'.$description.'</span>';
    }

    /**
     * @return array<string, string>
     */
    private static function declarationTypeSelectOptions(): array
    {
        /** @var array<string, array{label: string, description: string}> $definitions */
        $definitions = config('general.employee_benefit_declaration_types', []);

        $options = [];
        foreach (array_keys($definitions) as $value) {
            $options[(string) $value] = static::declarationTypeOptionLabel((string) $value);
        }

        return $options;
    }

    private static function declarationTypeOptionLabel(string $value): string
    {
        /** @var array<string, array{label: string, description: string}> $definitions */
        $definitions = config('general.employee_benefit_declaration_types', []);

        if (! isset($definitions[$value])) {
            return e($value);
        }

        $item = $definitions[$value];
        $label = e((string) ($item['label'] ?? $value));
        $description = e((string) ($item['description'] ?? ''));

        return '<strong>'.$label.'</strong>'
            .'<br><span style="color:#7f8c8d;font-size:12px">'.$description.'</span>';
    }
}
