<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerRegularPays;

use App\Filament\Clusters\DefaultPayOptions\DefaultPayOptionsCluster;
use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerRegularPays\Pages\ManageEmployerRegularPays;
use App\Filament\Exports\EmployerRegularPayExporter;
use App\Models\EmployerDefaultPayOption;
use App\Models\PayBasis;
use App\Models\PaySchedule;
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
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EmployerRegularPayResource extends Resource
{
    protected static ?string $model = EmployerDefaultPayOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = DefaultPayOptionsCluster::class;

    protected static ?string $navigationLabel = 'Regular Pay';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('employer_id')->default(fn () => auth()->id()),

                Section::make('Regular Pay')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('pay_schedule_id')
                                    ->label('Schedule')
                                    ->relationship(
                                        'paySchedule',
                                        'name',
                                        fn (Builder $query): Builder => $query->active()->orderBy('id'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get): void {
                                        $scheduleId = $get('pay_schedule_id');

                                        if (blank($scheduleId)) {
                                            $set('pay_bases_id', null);
                                            $set('annual_salary', null);
                                            static::syncPayCodeForBasis($set, null);

                                            return;
                                        }

                                        $defaultBasisId = PayBasis::query()
                                            ->active()
                                            ->where('pay_schedule_id', $scheduleId)
                                            ->orderBy('id')
                                            ->value('id');

                                        $set('pay_bases_id', $defaultBasisId);
                                        static::syncPayCodeForBasis($set, $defaultBasisId);

                                        if (! static::payScheduleShowsAnnualSalary($scheduleId)) {
                                            $set('annual_salary', null);
                                        }
                                    })
                                    ->nullable(),

                                Select::make('pay_bases_id')
                                    ->label('Basis')
                                    ->relationship(
                                        'payBasis',
                                        'description',
                                        fn (Builder $query, Get $get): Builder => $query->active()
                                            ->when(
                                                filled($get('pay_schedule_id')),
                                                fn (Builder $q): Builder => $q->where('pay_schedule_id', $get('pay_schedule_id')),
                                                fn (Builder $q): Builder => $q->whereRaw('0 = 1'),
                                            )
                                            ->orderBy('id'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                                        static::syncPayCodeForBasis($set, $state);

                                        if (static::payBasisKind($state) !== 'rate_annual' || ! static::payScheduleShowsAnnualSalary($get('pay_schedule_id'))) {
                                            $set('annual_salary', null);
                                        }
                                    })
                                    ->nullable(),
                            ])
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->visible(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['hourly', 'day_rate'], true))
                            ->schema([
                                TextInput::make('hourly_rate')
                                    ->label('Hourly Rate')
                                    ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                                    ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01'),
                                TextInput::make('hours_in_period')
                                    ->label(fn (Get $get): string => static::hoursInPeriodLabel($get('pay_schedule_id')))
                                    ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                                    ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                                    ->numeric()
                                    ->step('0.01'),
                                TextInput::make('day_rate')
                                    ->label('Day Rate')
                                    ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'day_rate')
                                    ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'day_rate')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01'),
                                TextInput::make('days_in_period')
                                    ->label(fn (Get $get): string => static::daysInPeriodLabel($get('pay_schedule_id')))
                                    ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'day_rate')
                                    ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'day_rate')
                                    ->numeric()
                                    ->step('0.01'),
                                TextInput::make('period_total')
                                    ->label('Period Total')
                                    ->dehydrated(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['hourly', 'day_rate'], true))
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01'),
                            ])
                            ->columnSpanFull(),

                        TextInput::make('period_amount')
                            ->label(fn (Get $get): string => static::periodAmountFieldLabel($get('pay_schedule_id'), $get('pay_bases_id')))
                            ->visible(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['rate_annual', 'fixed_period'], true))
                            ->dehydrated(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['rate_annual', 'fixed_period'], true))
                            ->numeric()
                            ->prefix('£')
                            ->step('0.01'),

                        TextInput::make('annual_salary')
                            ->label('Annual Salary')
                            ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'rate_annual'
                                && static::payScheduleShowsAnnualSalary($get('pay_schedule_id')))
                            ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'rate_annual'
                                && static::payScheduleShowsAnnualSalary($get('pay_schedule_id')))
                            ->numeric()
                            ->prefix('£')
                            ->step('0.01'),

                        Select::make('pay_code')
                            ->label('Pay Code')
                            ->options(fn (Get $get): array => static::payCodeOptionsForKind(static::payBasisKind($get('pay_bases_id'))))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('pro_rata_adjustment')
                            ->label('Pro-rata Adjustment')
                            ->visible(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['rate_annual', 'fixed_period'], true))
                            ->dehydrated(fn (Get $get): bool => in_array(static::payBasisKind($get('pay_bases_id')), ['rate_annual', 'fixed_period'], true))
                            ->options(
                                collect(config('general.pro_rata_adjustment', []))
                                    ->mapWithKeys(fn ($item, $key) => [
                                        $key => static::proRataAdjustmentOptionLabel((object) $item),
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value) => $value && isset(config('general.pro_rata_adjustment', [])[$value])
                                ? static::proRataAdjustmentOptionLabel((object) config('general.pro_rata_adjustment', [])[$value])
                                : null
                            )
                            ->allowHtml()
                            ->native(false)
                            ->searchable(),

                        Toggle::make('minimum_wage')
                            ->label('Minimum Wage')
                            ->visible(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                            ->dehydrated(fn (Get $get): bool => static::payBasisKind($get('pay_bases_id')) === 'hourly')
                            ->inline(false),

                    ])->columns(2)->columnSpanFull(),

                Section::make('Payroll Options')
                    ->schema([
                        Toggle::make('allow_negative_net_pay')
                            ->label('Allow Negative Net Pay'),

                        Toggle::make('automatically_calculate_back_pay_for_new_starters')
                            ->label('Automatically Calculate Back Pay for New Starters'),

                        Toggle::make('enable_paycode_validation')
                            ->label('Enable Pay Code Validation'),

                        Toggle::make('calculate_effective_date_salary_changes')
                            ->label('Calculate Effective Date Salary Changes'),

                        Toggle::make('group_paylines_on_payslip')
                            ->label('Group Paylines on Payslip'),

                        Toggle::make('sort_payroll_numbers_alpha_numerically')
                            ->label('Sort Payroll Numbers Alpha-Numerically'),
                    ])->columnSpanFull(),

                Section::make('Contracted Time')
                    ->schema([
                        TextInput::make('contracted_weeks')
                            ->label('Contracted Weeks (beta)')
                            ->maxLength(50),

                        TextInput::make('full_time_contracted_weeks')
                            ->label('Full Time Contracted Weeks (beta)')
                            ->maxLength(50),

                        TextInput::make('full_time_contracted_hours_per_week')
                            ->label('Full Time Contracted Hours Per Week (beta)')
                            ->maxLength(50),
                    ])->columns(3)->columnSpanFull(),

                Section::make('Base Rates')
                    ->columns(2)
                    ->schema([
                        TextInput::make('base_hourly_rate')
                            ->label(fn () => new HtmlString(
                                'Base Hourly Rate <span style="font-size: 10px; color: #7f8c8d">- for PayCodes that are multiples of this rate, e.g. overtime.</span>'
                            ))
                            ->hintColor(null)
                            ->numeric()
                            ->prefix('£')
                            ->maxLength(50),

                        TextInput::make('base_daily_rate')
                            ->label('Base Daily Rate')
                            ->numeric()
                            ->prefix('£')
                            ->maxLength(50),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pay_code')
            ->columns([
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('paySchedule.name')
                    ->label('Pay Schedule')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payBasis.name')
                    ->label('Pay Basis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pay_code')
                    ->label('Pay Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pro_rata_adjustment')
                    ->label('Pro-rata Adjustment')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'automatic' => 'Automatic',
                        'manual' => 'Manual',
                        default => $state ?? '-',
                    })
                    ->sortable(),

                TextColumn::make('period_amount')
                    ->label('Period Amount')
                    ->money(config('general.currency_code'))
                    ->sortable(),

                TextColumn::make('annual_salary')
                    ->label('Annual Salary')
                    ->money(config('general.currency_code'))
                    ->sortable(),

                TextColumn::make('contracted_weeks')
                    ->label('Contracted Weeks')
                    ->sortable(),

                TextColumn::make('full_time_contracted_weeks')
                    ->label('Full Time Weeks')
                    ->sortable(),

                TextColumn::make('full_time_contracted_hours_per_week')
                    ->label('Full Time Hours / Week')
                    ->sortable(),

                TextColumn::make('base_hourly_rate')
                    ->label('Base Hourly Rate')
                    ->money(config('general.currency_code'))
                    ->sortable(),

                TextColumn::make('base_daily_rate')
                    ->label('Base Daily Rate')
                    ->money(config('general.currency_code'))
                    ->sortable(),

                IconColumn::make('allow_negative_net_pay')
                    ->label('Negative Net Pay')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('automatically_calculate_back_pay_for_new_starters')
                    ->label('Calculate Back Pay for new Starters')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('enable_paycode_validation')
                    ->label('Paycode Validation')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('calculate_effective_date_salary_changes')
                    ->label('Effective Date Changes')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('group_paylines_on_payslip')
                    ->label('Group Paylines')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('sort_payroll_numbers_alpha_numerically')
                    ->label('Sort Payroll Numbers')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                EditAction::make()
                    ->modalWidth(Width::SixExtraLarge),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployerRegularPayExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employer', 'paySchedule', 'payBasis']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerRegularPays::route('/'),
        ];
    }

    private static function fetchPaySchedule(mixed $payScheduleId): ?PaySchedule
    {
        if (! filled($payScheduleId)) {
            return null;
        }

        return PaySchedule::query()
            ->whereKey($payScheduleId)
            ->first(['id', 'name', 'description', 'shows_annual_salary']);
    }

    private static function payScheduleShowsAnnualSalary(mixed $payScheduleId): bool
    {
        return (bool) static::fetchPaySchedule($payScheduleId)?->shows_annual_salary;
    }

    private static function periodAmountLabel(mixed $payScheduleId): string
    {
        $schedule = static::fetchPaySchedule($payScheduleId);

        if ($schedule === null || ! filled($schedule->description)) {
            return 'Period Amount';
        }

        return e(trim((string) $schedule->description)).' Amount';
    }

    /**
     * @return 'hourly'|'day_rate'|'rate_annual'|'fixed_period'|null
     */
    private static function payBasisKind(mixed $payBasisId): ?string
    {
        $basis = static::fetchPayBasis($payBasisId);
        if ($basis === null) {
            return null;
        }

        $name = strtolower(trim((string) $basis->name));
        $description = strtolower((string) ($basis->description ?? ''));

        if (str_contains($description, 'hourly rate')) {
            return 'hourly';
        }

        if (str_contains($description, 'same amount') || str_contains($description, 'every period')) {
            return 'fixed_period';
        }

        if (str_contains($description, 'day rate')) {
            return 'day_rate';
        }

        if (str_contains($description, 'annual salary') || str_contains($description, 'rate/annual')) {
            return 'rate_annual';
        }

        return match ($name) {
            'hourly' => 'hourly',
            'day' => 'day_rate',
            'rate/annual', 'rate_annual' => 'rate_annual',
            default => null,
        };
    }

    private static function fetchPayBasis(mixed $payBasisId): ?PayBasis
    {
        if (! filled($payBasisId)) {
            return null;
        }

        return PayBasis::query()
            ->whereKey($payBasisId)
            ->first(['id', 'name', 'description', 'pay_schedule_id']);
    }

    private static function hoursInPeriodLabel(mixed $payScheduleId): string
    {
        return 'Hours / '.static::schedulePeriodUnitPhrase($payScheduleId);
    }

    private static function daysInPeriodLabel(mixed $payScheduleId): string
    {
        return 'Days / '.static::schedulePeriodUnitPhrase($payScheduleId);
    }

    private static function schedulePeriodUnitPhrase(mixed $payScheduleId): string
    {
        $name = strtolower((string) (PaySchedule::query()->whereKey($payScheduleId)->value('name') ?? ''));
        $collapsed = str_replace([' ', '-', '_'], '', $name);

        return match (true) {
            $name === 'monthly' => 'month',
            $name === 'weekly' => 'week',
            $name === 'daily' => 'day',
            str_contains($name, 'fortnight') => 'fortnight',
            str_contains($collapsed, 'fourweekly') => '4 wks',
            str_contains($name, 'four') && str_contains($name, 'week') => '4 wks',
            $name === 'custom' => 'week',
            default => 'period',
        };
    }

    private static function periodAmountFieldLabel(mixed $payScheduleId, mixed $payBasisId): string
    {
        if (static::payBasisKind($payBasisId) === 'fixed_period') {
            return 'Period Amount';
        }

        return static::periodAmountLabel($payScheduleId);
    }

    /**
     * @return array<string, string>
     */
    private static function payCodeOptionsForKind(?string $kind): array
    {
        $all = [
            'BASIC' => 'Basic',
            'BASICHOURLY' => 'Basic Hourly',
            'BASICDAILY' => 'Basic Daily',
        ];

        return match ($kind) {
            'hourly' => [
                'BASICHOURLY' => $all['BASICHOURLY'],
            ],
            'day_rate' => [
                'BASICDAILY' => $all['BASICDAILY'],
            ],
            'rate_annual', 'fixed_period' => [
                'BASIC' => $all['BASIC'],
            ],
            default => $all,
        };
    }

    private static function syncPayCodeForBasis(Set $set, mixed $payBasisId): void
    {
        if (! filled($payBasisId)) {
            $set('pay_code', null);

            return;
        }

        $kind = static::payBasisKind($payBasisId);
        if ($kind === null) {
            return;
        }

        $set('pay_code', match ($kind) {
            'hourly' => 'BASICHOURLY',
            'day_rate' => 'BASICDAILY',
            'rate_annual', 'fixed_period' => 'BASIC',
        });
    }

    private static function proRataAdjustmentOptionLabel(object $record): string
    {
        $description = e((string) ($record->description ?? ''));

        return '<span style="font-weight: 600;">'.e($record->name).'</span>'
            .' <span style="color: #7f8c8d; font-size: 12px;">'.$description.'</span>';
    }
}
