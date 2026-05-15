<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeAdditionDeductions;

use App\Filament\Clusters\PayOptions\PayOptionsCluster;
use App\Filament\Clusters\PayOptions\Resources\EmployeeAdditionDeductions\Pages\ManageEmployeeAdditionDeductions;
use App\Filament\Exports\EmployeeAdditionDeductionExporter;
use App\Models\EmployeeAdditionDeduction;
use App\Models\PayCode;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class EmployeeAdditionDeductionResource extends Resource
{
    protected static ?string $model = EmployeeAdditionDeduction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = PayOptionsCluster::class;

    protected static ?string $navigationLabel = 'Additions & Deductions';

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?int $navigationSort = 2;

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
                        Grid::make(4)
                            ->schema([
                                Select::make('pay_code_id')
                                    ->label('Code')
                                    ->options(fn (): array => static::payCodeSelectOptions())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->allowHtml()
                                    ->columnSpan(fn (Get $get): int => match (static::lineKind($get)) {
                                        'fixed_period' => 2,
                                        'fixed_annual' => 2,
                                        'hourly' => 1,
                                        'daily' => 1,
                                        default => 4,
                                    })
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $description = PayCode::query()
                                            ->whereKey($state)
                                            ->value('description');

                                        if (filled($description)) {
                                            $set('description', $description);
                                        }
                                    }),

                                TextInput::make('fixed_period_amount')
                                    ->label('Add Fixed Period Amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => static::lineKind($get) === 'fixed_period')
                                    ->dehydrated(fn (Get $get): bool => static::lineKind($get) === 'fixed_period'),

                                TextInput::make('fixed_annual_amount')
                                    ->label('Add Fixed Annual Amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => static::lineKind($get) === 'fixed_annual')
                                    ->dehydrated(fn (Get $get): bool => static::lineKind($get) === 'fixed_annual'),

                                TextInput::make('quantity')
                                    ->label(fn (Get $get): string => static::lineKind($get) === 'hourly' ? 'Hours' : 'Days')
                                    ->numeric()
                                    ->step('0.01')
                                    ->suffix(fn (Get $get): string => static::lineKind($get) === 'hourly' ? 'hrs' : 'days')
                                    ->live()
                                    ->columnSpan(1)
                                    ->visible(fn (Get $get): bool => in_array(static::lineKind($get), ['hourly', 'daily'], true))
                                    ->dehydrated(fn (Get $get): bool => in_array(static::lineKind($get), ['hourly', 'daily'], true)),

                                TextInput::make('rate')
                                    ->label(fn (Get $get): string => static::lineKind($get) === 'hourly' ? 'Hourly Rate' : 'Day Rate')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->live()
                                    ->columnSpan(1)
                                    ->visible(fn (Get $get): bool => in_array(static::lineKind($get), ['hourly', 'daily'], true))
                                    ->dehydrated(fn (Get $get): bool => in_array(static::lineKind($get), ['hourly', 'daily'], true)),

                                Placeholder::make('line_total_preview')
                                    ->label('Total')
                                    ->content(fn (Get $get): HtmlString => static::lineTotalPreviewContent($get))
                                    ->columnSpan(1)
                                    ->visible(fn (Get $get): bool => in_array(static::lineKind($get), ['hourly', 'daily'], true)),
                            ])
                            ->columnSpanFull(),

                        Toggle::make('gross_up_target_net')
                            ->label('Gross up Target Net')
                            ->visible(fn (Get $get): bool => in_array(static::lineKind($get), ['fixed_period', 'fixed_annual'], true))
                            ->dehydrated(fn (Get $get): bool => in_array(static::lineKind($get), ['fixed_period', 'fixed_annual'], true))
                            ->columnSpanFull(),

                        Select::make('pro_rata_adjustment')
                            ->label('Pro-rata Adjustments')
                            ->options(
                                collect(config('general.pro_rata_adjustment', []))
                                    ->mapWithKeys(fn (mixed $item, string $key): array => [
                                        $key => static::proRataAdjustmentOptionLabel((object) $item),
                                    ])
                                    ->all()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => $value && isset(config('general.pro_rata_adjustment', [])[$value])
                                ? static::proRataAdjustmentOptionLabel((object) config('general.pro_rata_adjustment', [])[$value])
                                : null)
                            ->allowHtml()
                            ->native(false)
                            ->searchable()
                            ->visible(fn (Get $get): bool => in_array(static::lineKind($get), ['fixed_period', 'fixed_annual'], true))
                            ->dehydrated(fn (Get $get): bool => in_array(static::lineKind($get), ['fixed_period', 'fixed_annual'], true))
                            ->default('automatic')
                            ->columnSpanFull(),

                        TextInput::make('full_time_annual_value')
                            ->label('Full Time Annual Value')
                            ->numeric()
                            ->prefix('£')
                            ->step('0.01')
                            ->default('0.00')
                            ->visible(fn (Get $get): bool => static::lineKind($get) === 'fixed_annual')
                            ->dehydrated(fn (Get $get): bool => static::lineKind($get) === 'fixed_annual')
                            ->columnSpanFull(),

                        TextInput::make('description')
                            ->label('Description')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('effective_from')
                                    ->label('Effective From (optional)')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                DatePicker::make('effective_to')
                                    ->label('Effective To (optional)')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                            ])
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

                TextColumn::make('payCode.name')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('fixed_period_amount')
                    ->label('Period amount')
                    ->money('GBP')
                    ->sortable(),

                TextColumn::make('fixed_annual_amount')
                    ->label('Annual amount')
                    ->money('GBP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quantity')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rate')
                    ->money('GBP')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('gross_up_target_net')
                    ->label('Gross up')
                    ->boolean(),

                TextColumn::make('pro_rata_adjustment')
                    ->label('Pro-rata')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'manual' => 'Manual',
                        'automatic' => 'Automatic',
                        default => '-',
                    }),

                TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),

                TextColumn::make('effective_to')
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
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Line')
                    ->modalWidth(Width::FourExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployeeAdditionDeductionExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeAdditionDeductions::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    private static function payCodeSelectOptions(): array
    {
        /** @var list<string> $order */
        $order = config('general.employee_addition_deduction_pay_code_order', []);

        $payCodes = PayCode::query()
            ->whereIn('name', $order)
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        return $payCodes
            ->sortBy(function (PayCode $payCode) use ($order): int {
                $index = array_search($payCode->name, $order, true);

                return $index === false ? 999 : $index;
            })
            ->mapWithKeys(fn (PayCode $payCode): array => [
                $payCode->id => static::payCodeOptionLabel($payCode),
            ])
            ->all();
    }

    private static function payCodeOptionLabel(PayCode $payCode): string
    {
        $description = e((string) ($payCode->description ?? ''));

        return '<span style="font-weight: 600;">'.e($payCode->name).'</span>'
            .' <span style="color: #7f8c8d; font-size: 12px;">'.$description.'</span>';
    }

    private static function proRataAdjustmentOptionLabel(object $record): string
    {
        $description = e((string) ($record->description ?? ''));

        return '<span style="font-weight: 600;">'.e($record->name).'</span>'
            .' <span style="color: #7f8c8d; font-size: 12px;">'.$description.'</span>';
    }

    private static function lineKind(Get $get): ?string
    {
        $payCodeId = $get('pay_code_id');

        if (blank($payCodeId)) {
            return null;
        }

        $name = PayCode::query()->whereKey($payCodeId)->value('name');

        if (! is_string($name) || $name === '') {
            return null;
        }

        /** @var array<string, string> $kinds */
        $kinds = config('general.employee_addition_deduction_line_kinds', []);

        return $kinds[$name] ?? null;
    }

    private static function lineTotalPreviewContent(Get $get): HtmlString
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $rate = (float) ($get('rate') ?? 0);
        $total = number_format($quantity * $rate, 2, '.', '');

        return new HtmlString('<span class="text-base font-medium text-gray-950 dark:text-white">'.$total.'</span>');
    }
}
