<?php

namespace App\Filament\Resources\EmployerBankAccounts;

use App\Filament\Exports\EmployerBankAccountExporter;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\EmployerBankAccounts\Pages\ManageEmployerBankAccounts;
use App\Models\BankCsvFormat;
use App\Models\EmployerBankAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class EmployerBankAccountResource extends Resource
{
    protected static ?string $model = EmployerBankAccount::class;
    protected static \UnitEnum|string|null $navigationGroup = 'Employers';
    protected static ?string $navigationLabel = 'Bank Account';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employer Bank Account Details')
                    ->schema([
                        Hidden::make('employer_id')
                            ->default(fn (): ?int => auth()->user()->id ?: null)
                            ->dehydrated()
                            ->required(fn (): bool => filled(auth()->user()->id)),

                        Select::make('employer_id')
                            ->label('Employer')
                            ->relationship('employer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->hidden(fn (): bool => filled(auth()->user()->id)),

                        TextInput::make('bank_name'),
                        TextInput::make('bank_branch'),
                        TextInput::make('bank_reference'),

                        Select::make('country_of_bank')
                            ->options(
                                collect(config('general.countries_of_bank'))
                                    ->mapWithKeys(fn ($value, $key) => [
                                        $key => "<span>$value</span> <span style='color: #7f8c8d; font-size: 12px'>$key</span>",
                                    ])->toArray())
                            ->searchable()
                            ->allowHtml(),

                        TextInput::make('building_society_reference'),
                    ])->columns(3)->columnSpan(2),

                Section::make('Employer Account Details')
                    ->schema([
                        TextInput::make('account_name'),
                        TextInput::make('account_number')
                            ->label('Account number')
                            ->inputMode('numeric')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($livewire, TextInput $component): void {
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->maxLength(8)
                            ->minLength(8)
                            ->regex('/^\d{8}$/')
                            ->validationMessages([
                                'min' => 'Account number must be exactly 8 digits.',
                                'max' => 'Account number must be exactly 8 digits.',
                                'regex' => 'Account number must be exactly 8 digits.',
                            ]),
                        TextInput::make('sort_code')
                            ->label('Sort code')
                            ->inputMode('numeric')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($livewire, TextInput $component): void {
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->maxLength(6)
                            ->minLength(6)
                            ->regex('/^\d{6}$/')
                            ->validationMessages([
                                'min' => 'Sort code must be exactly 6 digits.',
                                'max' => 'Sort code must be exactly 6 digits.',
                                'regex' => 'Sort code must be exactly 6 digits.',
                            ]),
                        TextInput::make('iban'),
                        TextInput::make('swift_bic'),

                    ])->columns(3)->columnSpan(2),

                Section::make('Employer Bank Account Details')
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
                            ->getOptionLabelFromRecordUsing(function (BankCsvFormat $record): string {
                                $description = e($record->description ?? '');

                                return '<span>'.e($record->name).'</span> <span style="color: #7f8c8d; font-size: 12px">'.$description.'</span>';
                            })
                            ->allowHtml()
                            ->live()

                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                if (! static::bankCsvFormatRequiresBacsSun($state)) {
                                    $set('bacs_sun', null);
                                }
                            })->columnSpanFull(),

                        TextInput::make('bacs_sun')
                            ->maxLength(20)
                            ->visible(fn (Get $get): bool => static::bankCsvFormatRequiresBacsSun($get('bank_payment_csv_format_id')))
                            ->dehydrated(fn (Get $get): bool => static::bankCsvFormatRequiresBacsSun($get('bank_payment_csv_format_id')))
                            ->columnSpan(1),

                        TextInput::make('payment_reference_format')->columnSpan(1),

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
    protected static function bankCsvFormatRequiresBacsSun(mixed $bankCsvFormatId): bool
    {
        if (blank($bankCsvFormatId)) {
            return false;
        }

        return (bool) BankCsvFormat::query()->whereKey($bankCsvFormatId)->value('requires_bacs_sun');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bank_name')
            ->columns([
                TextColumn::make('employer_id')->numeric()->sortable(),
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
}
