<?php

namespace App\Filament\Resources\EmployerBankAccounts;

use App\Filament\Exports\EmployerBankAccountExporter;
use App\Filament\Resources\EmployerBankAccounts\Pages\ManageEmployerBankAccounts;
use App\Models\BankCsvFormat;
use App\Models\EmployerBankAccount;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

class EmployerBankAccountResource extends Resource
{
    private const int ACCOUNT_NUMBER_DIGITS = 8;

    private const int SORT_CODE_DIGITS = 6;

    protected static ?string $model = EmployerBankAccount::class;

    protected static ?string $recordTitleAttribute = 'bank_name';

    protected static UnitEnum|string|null $navigationGroup = 'Employers';

    protected static ?string $navigationLabel = 'Bank Account';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employer Bank Account Details')
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

                        TextInput::make('bank_name'),
                        TextInput::make('bank_branch'),
                        TextInput::make('bank_reference'),

                        Select::make('country_of_bank')
                            ->options(static::countryOfBankOptions())
                            ->searchable()
                            ->allowHtml(),

                        TextInput::make('building_society_reference'),
                    ])->columns(3)->columnSpan(2),

                Section::make('Employer Account Details')
                    ->schema([
                        TextInput::make('account_name'),
                        static::numericDigitsTextInput(
                            name: 'account_number',
                            label: 'Account number',
                            digits: self::ACCOUNT_NUMBER_DIGITS,
                            exactLengthMessage: 'Account number must be exactly 8 digits.',
                        ),
                        static::numericDigitsTextInput(
                            name: 'sort_code',
                            label: 'Sort code',
                            digits: self::SORT_CODE_DIGITS,
                            exactLengthMessage: 'Sort code must be exactly 6 digits.',
                        ),
                        TextInput::make('iban'),
                        TextInput::make('swift_bic'),

                    ])->columns(3)->columnSpan(2),

                Section::make('CSV format and payment references')
                    ->schema([

                        Select::make('bank_payment_csv_format_id')
                            ->label('Bank payment CSV format')
                            ->relationship(
                                'bankPaymentCsvFormat',
                                'name',
                                fn (Builder $query): Builder => $query->active()->orderBy('name'),
                            )
                            ->searchable(['name', 'description'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (BankCsvFormat $record): string => static::bankCsvFormatOptionLabel($record))
                            ->allowHtml()
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                if (! static::bankCsvFormatRequiresBacsSun($state)) {
                                    $set('bacs_sun', null);
                                }
                            })->columnSpanFull(),

                        TextInput::make('bacs_sun')
                            ->maxLength(20)
                            ->visible(fn (Get $get): bool => static::bacsSunApplies($get))
                            ->dehydrated(fn (Get $get): bool => static::bacsSunApplies($get))
                            ->columnSpan(1),

                        TextInput::make('payment_reference_format')
                            ->label(new HtmlString(
                                'Payment Reference Format'
                                .' <span style="color:#6b7280;font-size:0.75rem;font-weight:400;white-space:nowrap">'
                                .'— to appear as the reference on BACS payments to employees'
                                .'</span>'
                            ))
                            ->placeholder('{period} {periodNumber} : {payrollCode}')
                            ->columnSpan(1),

                    ])->columns(2)->columnSpan(2),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('reject_invalid_employee_bank_details'),
                        Toggle::make('include_attachment_of_earnings'),
                        Toggle::make('include_deductions'),
                        Toggle::make('include_hmrc_payment'),
                        Toggle::make('include_pensions'),
                    ])->columns(2)->columnSpan(2),

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employer', 'bankPaymentCsvFormat']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bank_name')
                    ->searchable(),
                TextColumn::make('bank_branch')
                    ->searchable(),
                TextColumn::make('bank_reference')
                    ->searchable(),
                TextColumn::make('account_name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_code')
                    ->searchable(),
                TextColumn::make('building_society_reference')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country_of_bank')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('iban')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('swift_bic')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bankPaymentCsvFormat.name')
                    ->label('CSV format')
                    ->searchable()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bacs_sun')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_reference_format')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('reject_invalid_employee_bank_details')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('include_attachment_of_earnings')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('include_deductions')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('include_hmrc_payment')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('include_pensions')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
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

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployerBankAccountExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerBankAccounts::route('/'),
        ];
    }

    protected static function bankCsvFormatRequiresBacsSun(mixed $bankCsvFormatId): bool
    {
        if (blank($bankCsvFormatId)) {
            return false;
        }

        return (bool) BankCsvFormat::query()->whereKey($bankCsvFormatId)->value('requires_bacs_sun');
    }

    private static function bacsSunApplies(Get $get): bool
    {
        return static::bankCsvFormatRequiresBacsSun($get('bank_payment_csv_format_id'));
    }

    private static function bankCsvFormatOptionLabel(BankCsvFormat $record): string
    {
        $description = e($record->description ?? '');

        return '<span>'.e($record->name).'</span> <span style="color: #7f8c8d; font-size: 12px">'.$description.'</span>';
    }

    private static function countryOfBankOptions(): array
    {
        return collect(config('general.countries_of_bank', []))
            ->mapWithKeys(fn (string $value, string $key): array => [
                $key => '<span>'.e($value).'</span> <span style="color: #7f8c8d; font-size: 12px">'.e($key).'</span>',
            ])
            ->all();
    }

    private static function numericDigitsTextInput(string $name, string $label, int $digits, string $exactLengthMessage): TextInput
    {
        $pattern = '/^\d{'.$digits.'}$/';

        return TextInput::make($name)
            ->label($label)
            ->inputMode('numeric')
            ->live(debounce: 500)
            ->afterStateUpdated(function ($livewire, TextInput $component): void {
                $livewire->validateOnly($component->getStatePath());
            })
            ->maxLength($digits)
            ->minLength($digits)
            ->regex($pattern)
            ->validationMessages([
                'min' => $exactLengthMessage,
                'max' => $exactLengthMessage,
                'regex' => $exactLengthMessage,
            ]);
    }
}
