<?php

namespace App\Filament\Clusters\PayOptions\Resources\EmployeeLoans;

use App\Filament\Clusters\PayOptions\PayOptionsCluster;
use App\Filament\Clusters\PayOptions\Resources\EmployeeLoans\Pages\ManageEmployeeLoans;
use App\Filament\Exports\EmployeeLoanExporter;
use App\Filament\Support\EmployeeRecordDocumentUploads;
use App\Models\EmployeeLoan;
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

class EmployeeLoanResource extends Resource
{
    protected static ?string $model = EmployeeLoan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = PayOptionsCluster::class;

    protected static ?string $navigationLabel = 'Loans';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?string $modelLabel = 'loan';

    protected static ?string $pluralModelLabel = 'loans';

    protected static ?int $navigationSort = 3;

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
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('issue_date')
                                    ->label('Issue Date')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),

                                TextInput::make('reference')
                                    ->label('Reference (optional)')
                                    ->maxLength(150)
                                    ->columnSpan(1),
                            ])
                            ->columnSpanFull(),

                        Select::make('pay_code_id')
                            ->label('Pay Code')
                            ->options(fn (): array => static::payCodeSelectOptions())
                            ->searchable()
                            ->preload()
                            ->allowHtml()
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('pause_payments')
                            ->label('Pause Payments')
                            ->helperText('Temporarily stop making deductions for this loan.')
                            ->inline(false)
                            ->default(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('loan_amount')
                                    ->label('Loan Amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get): void {
                                        static::syncBalanceField($set, $get);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('previously_paid')
                                    ->label('Previously Paid')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get): void {
                                        static::syncBalanceField($set, $get);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('period_amount')
                                    ->label('Period Amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->columnSpan(1),

                                TextInput::make('amount_repaid')
                                    ->label('Amount Repaid')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->disabled()
                                    ->dehydrated()
                                    ->extraInputAttributes(['class' => 'bg-gray-100 dark:bg-gray-800'])
                                    ->columnSpan(1),

                                TextInput::make('balance')
                                    ->label('Balance')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step('0.01')
                                    ->default('0.00')
                                    ->disabled()
                                    ->dehydrated()
                                    ->extraInputAttributes(['class' => 'bg-gray-100 dark:bg-gray-800'])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make()
                    ->schema([
                        EmployeeRecordDocumentUploads::fileUpload('employee-loan-documents'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(EmployeeRecordDocumentUploads::eagerLoadDocuments())
            ->recordTitleAttribute('reference')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'middle_name', 'last_name']),

                TextColumn::make('issue_date')
                    ->label('Issue date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('payCode.name')
                    ->label('Pay code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reference')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('loan_amount')
                    ->label('Loan')
                    ->money('GBP')
                    ->sortable(),

                TextColumn::make('period_amount')
                    ->label('Period')
                    ->money('GBP')
                    ->sortable(),

                TextColumn::make('amount_repaid')
                    ->label('Repaid')
                    ->money('GBP')
                    ->sortable(),

                TextColumn::make('balance')
                    ->money('GBP')
                    ->sortable(),

                IconColumn::make('pause_payments')
                    ->label('Paused')
                    ->boolean(),

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
                    ->modalHeading('Edit Loan')
                    ->modalWidth(Width::FourExtraLarge)
                    ->mutateRecordDataUsing(EmployeeRecordDocumentUploads::mutateEditFormRecordDataUsing(EmployeeLoan::class))
                    ->using(EmployeeRecordDocumentUploads::editSaveUsing(EmployeeLoan::class)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployeeLoanExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeLoans::route('/'),
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
        $order = config('general.employee_loan_pay_code_order', []);

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

    private static function syncBalanceField(Set $set, Get $get): void
    {
        $loan = (float) ($get('loan_amount') ?? 0);
        $previouslyPaid = (float) ($get('previously_paid') ?? 0);
        $repaid = (float) ($get('amount_repaid') ?? 0);
        $balance = $loan - $previouslyPaid - $repaid;

        $set('balance', number_format($balance, 2, '.', ''));
    }
}
