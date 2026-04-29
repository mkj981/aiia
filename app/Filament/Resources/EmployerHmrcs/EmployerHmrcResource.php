<?php

namespace App\Filament\Resources\EmployerHmrcs;

use App\Filament\Exports\EmployerHmrcExporter;
use App\Filament\Resources\EmployerHmrcs\Pages\ManageEmployerHmrcs;
use App\Models\EmployerHmrc;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

class EmployerHmrcResource extends Resource
{
    protected static ?string $model = EmployerHmrc::class;

    protected static ?string $recordTitleAttribute = 'paye_office_number';

    protected static UnitEnum|string|null $navigationGroup = 'Employers';

    protected static ?string $navigationLabel = 'HMRC';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('PAYE Details')
                    ->schema([

                        Grid::make(12)
                            ->schema([

                                Hidden::make('employer_id')
                                    ->default(fn (): ?int => auth()->id())
                                    ->dehydrated()
                                    ->required(fn (): bool => filled(auth()->id())),

                                Select::make('employer_id')
                                    ->label('Employer')
                                    ->relationship('employer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->hidden(fn (): bool => filled(auth()->id())),

                                Placeholder::make('employer_paye_reference_label')
                                    ->hiddenLabel()
                                    ->content('Employer PAYE Reference')
                                    ->extraAttributes([
                                        'class' => 'pt-2 text-sm text-gray-600',
                                    ])
                                    ->columnSpan(3),

                                TextInput::make('paye_office_number')
                                    ->hiddenLabel()
                                    ->columnSpan(2),

                                Placeholder::make('slash')
                                    ->hiddenLabel()
                                    ->content('/')
                                    ->extraAttributes([
                                        'class' => 'pt-2 text-center text-gray-600',
                                    ])
                                    ->columnSpan(1),

                                TextInput::make('paye_reference')
                                    ->hiddenLabel()
                                    ->columnSpan(4),
                            ])
                            ->columnSpanFull(),

                        TextInput::make('accounts_office_reference')->label('Accounts Office Reference'),
                        TextInput::make('econ_number')->label('ECON Number'),
                        TextInput::make('utr')->label('UTR'),
                        TextInput::make('corporation_tax_reference')->label('Corporation Tax Reference'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('PAYE Details')
                    ->schema([

                        Grid::make(2)
                            ->schema([
                                Radio::make('payment_schedule')
                                    ->label('Payment Schedule')
                                    ->options(config('general.payment_schedule'))
                                    ->default('monthly')
                                    ->inline()
                                    ->live(),

                                Toggle::make('carry_forward_unpaid_liabilities')
                                    ->label('Carry Forward Unpaid Liabilities')
                                    ->inline(false)
                                    ->columnSpan(1),

                                Placeholder::make('payment_schedule_warning')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('
                <div style="
                    margin-top: 8px;
                    background-color: #f8d7da;
                    border: 1px solid #f5c2c7;
                    color: #842029;
                    padding: 12px 16px;
                    border-radius: 6px;
                    font-size: 14px;
                ">
                    <strong>Warning:</strong>
                    Changing the Payment Schedule will delete any payment or adjustment information that you may have manually entered for the 2025/26 Tax Year.
                </div>
            '))
                                    ->visible(fn (Get $get) => $get('payment_schedule') === 'quarterly'),
                            ])->columnSpanFull(),

                        Select::make('payment_date_type')
                            ->options(config('general.payment_date_type'))->live()
                            ->afterStateUpdated(fn ($state, $set) => $state !== 'date_of_month' ? $set('payment_day_of_month', null) : null
                            ),

                        Select::make('payment_day_of_month')
                            ->options(static::getDayOfMonth())->searchable()
                            ->visible(fn (Get $get) => $get('payment_date_type') === 'date_of_month')
                            ->dehydrated(fn (Get $get) => $get('payment_date_type') === 'date_of_month'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Relief & Allowances')
                    ->schema([
                        Toggle::make('qualifies_for_small_employers_relief')
                            ->label('Employer qualifies for Small Employers Relief')
                            ->columnSpanFull(),

                        Toggle::make('eligible_for_employment_allowance')
                            ->label('Employer is eligible to claim Employment Allowance')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('employment_allowance_max_claim')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (Get $get) => (bool) $get('eligible_for_employment_allowance'))
                            ->dehydrated(fn (Get $get) => (bool) $get('eligible_for_employment_allowance')),

                        Toggle::make('include_employment_allowance_on_monthly_journal')
                            ->label('Include Allowance in Monthly Journal')
                            ->visible(fn (Get $get) => (bool) $get('eligible_for_employment_allowance'))
                            ->dehydrated(fn (Get $get) => (bool) $get('eligible_for_employment_allowance')),

                        Toggle::make('required_to_pay_apprenticeship_levy')
                            ->label('Employer is required to pay the Apprenticeship Levy')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('apprenticeship_levy_allowance')
                            ->label('Apprenticeship Levy Allowance')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('£')
                            ->inlinePrefix()
                            ->visible(fn (Get $get) => (bool) $get('required_to_pay_apprenticeship_levy'))
                            ->dehydrated(fn (Get $get) => (bool) $get('required_to_pay_apprenticeship_levy')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('employer');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('paye_office_number')
            ->columns([
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('paye_office_number')
                    ->label('PAYE office')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('paye_reference')
                    ->label('PAYE ref')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_schedule')
                    ->label('Schedule')
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : (config('general.payment_schedule')[$state] ?? $state))
                    ->sortable(),
                TextColumn::make('payment_date_type')
                    ->label('Pay date type')
                    ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : (config('general.payment_date_type')[$state] ?? $state))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_day_of_month')
                    ->label('Day of month')
                    ->formatStateUsing(function ($state): ?string {
                        if ($state === null || $state === '') {
                            return null;
                        }

                        $options = static::getDayOfMonth();

                        return $options[(int) $state] ?? (string) $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('qualifies_for_small_employers_relief')
                    ->label('SER')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('eligible_for_employment_allowance')
                    ->label('EA')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employment_allowance_max_claim')
                    ->label('Max EA')
                    ->money('GBP')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('include_employment_allowance_on_monthly_journal')
                    ->label('EA in journal')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('required_to_pay_apprenticeship_levy')
                    ->label('Levy')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('apprenticeship_levy_allowance')
                    ->label('Levy allowance')
                    ->money('GBP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('accounts_office_reference')
                    ->searchable()
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

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployerHmrcExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerHmrcs::route('/'),
        ];
    }

    public static function getDayOfMonth(): array
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = date('jS', strtotime("2023-01-$i"));
        }

        return $days;
    }
}
