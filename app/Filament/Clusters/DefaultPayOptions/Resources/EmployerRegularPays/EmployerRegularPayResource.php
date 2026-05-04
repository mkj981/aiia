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
    private const array PAY_CODE_OPTIONS = ['basic' => 'BASIC'];

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

                                    return;
                                }

                                $set(
                                    'pay_bases_id',
                                    PayBasis::query()
                                        ->active()
                                        ->where('pay_schedule_id', $scheduleId)
                                        ->orderByDesc('id')
                                        ->value('id'),
                                );

                                if (! static::payScheduleShowsAnnualSalary($scheduleId)) {
                                    $set('annual_salary', null);
                                }
                            })
                            ->nullable(),

                        Select::make('pay_bases_id')
                            ->label('Pay Basis')
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
                            ->nullable(),

                        TextInput::make('period_amount')
                            ->label(fn (Get $get): string => static::periodAmountLabel($get('pay_schedule_id')))
                            ->numeric()
                            ->prefix('£')
                            ->step('0.01'),

                        TextInput::make('annual_salary')
                            ->label('Annual Salary')
                            ->visible(fn (Get $get): bool => static::payScheduleShowsAnnualSalary($get('pay_schedule_id')))
                            ->numeric()
                            ->prefix('£')
                            ->step('0.01'),

                        Select::make('pay_code')
                            ->label('Pay Code')
                            ->options(self::PAY_CODE_OPTIONS)
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('pro_rata_adjustment')
                            ->label('Pro-rata Adjustment')
                            ->options(
                                collect(config('general.pro_rata_adjustment'))
                                    ->mapWithKeys(fn ($item, $key) => [
                                        $key => static::proRataAdjustmentOptionLabel((object) $item),
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value) => $value && isset(config('general.pro_rata_adjustment')[$value])
                                ? static::proRataAdjustmentOptionLabel((object) config('general.pro_rata_adjustment')[$value])
                                : null
                            )
                            ->allowHtml()
                            ->native(false)
                            ->searchable(),

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
            ->first(['id', 'description', 'shows_annual_salary']);
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

    private static function proRataAdjustmentOptionLabel(object $record): string
    {
        return '
        <div>
            <div>'.e($record->name).'</div>
            <div style="color: #7f8c8d; font-size: 12px;">'.e($record->description ?? '').'</div>
        </div>
    ';
    }
}
