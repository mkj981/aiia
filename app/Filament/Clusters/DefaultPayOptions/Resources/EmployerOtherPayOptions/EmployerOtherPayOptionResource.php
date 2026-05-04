<?php

namespace App\Filament\Clusters\DefaultPayOptions\Resources\EmployerOtherPayOptions;

use App\Filament\Clusters\DefaultPayOptions\DefaultPayOptionsCluster;
use App\Filament\Clusters\DefaultPayOptions\Resources\EmployerOtherPayOptions\Pages\ManageEmployerOtherPayOptions;
use App\Filament\Exports\EmployerOtherPayOptionExporter;
use App\Models\EmployerOtherPayOptions;
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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EmployerOtherPayOptionResource extends Resource
{
    private const POSTGRAD_LOAN = ['yes' => 'Yes', 'no' => 'No'];
    protected static ?string $model = EmployerOtherPayOptions::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $cluster = DefaultPayOptionsCluster::class;
    protected static ?string $navigationLabel = 'Other';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Other Pay Settings')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                        Hidden::make('employer_id')
                            ->default(fn (): ?int => auth()->id())
                            ->dehydrated()
                            ->required(fn (): bool => filled(auth()->id())),

                        Select::make('student_loan_plan')
                            ->options(config('general.Employer_student_loan')),

                        Select::make('postgrad_loan')
                            ->options(self::POSTGRAD_LOAN),


                        Select::make('hours_normally_worked_band')
                            ->options(config('general.hours_normally_worked_band')),

                        Select::make('payment_method')
                            ->options(config('general.payment_method')),

                        Select::make('vehicle_type')
                            ->options(config('general.vehicle_type')),


                        Toggle::make('withhold_tax_refund_if_gross_pay_zero')
                            ->label(new HtmlString('
                                        Withhold Tax Refund
                                        <span style="font-size:12px; color:#6b7280;">
                                            Dont allow a negative PAYE amount if the gross pay is zero
                                        </span>
                                    '))
                            ->columnSpanFull(),

                        Toggle::make('off_payroll_worker')
                            ->label(new HtmlString('
                                       Off-Payroll Worker
                                        <span style="font-size:12px; color:#6b7280;">
                                            Employee is an off-payroll worker subject to 2020 rules
                                        </span>
                                    '))
                            ->columnSpanFull(),

                        Toggle::make('irregular_payment_pattern')
                            ->label(new HtmlString('
                                       Irregular Payment Pattern
                                        <span style="font-size:12px; color:#6b7280;">
                                            Employee is currently on an irregular payment pattern
                                        </span>
                                    '))
                            ->columnSpanFull(),

                        Toggle::make('non_individual')
                            ->label(new HtmlString('
                                       Non-Individual
                                        <span style="font-size:12px; color:#6b7280;">
                                            Employees payments are being made to a body (eg, trustee, corporate organisation or personal representative)
                                        </span>
                                    '))
                            ->columnSpanFull(),

                        Toggle::make('exclude_from_rti_submissions')
                            ->label(new HtmlString('
                                       Exclude from RTI Submissions
                                        <span style="font-size:12px; color:#6b7280;">
                                            Employee will not be included on any submissions to HMRC
                                        </span>
                                    '))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('employer_id')
            ->columns([
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student_loan_plan')
                    ->label('Student Loan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('postgrad_loan')
                    ->label('Postgrad Loan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hours_normally_worked_band')
                    ->label('Hours Band')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->sortable(),

                TextColumn::make('vehicle_type')
                    ->label('Vehicle Type')
                    ->sortable(),

                IconColumn::make('withhold_tax_refund_if_gross_pay_zero')
                    ->label('Withhold Refund')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('off_payroll_worker')
                    ->label('Off Payroll')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('irregular_payment_pattern')
                    ->label('Irregular Pay')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('non_individual')
                    ->label('Non Individual')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('exclude_from_rti_submissions')
                    ->label('Exclude RTI')
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(EmployerOtherPayOptionExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerOtherPayOptions::route('/'),
        ];
    }

    /**
     * @return Builder
     * When renders a table it uses getEloquentQuery() as the base query for fetching all rows. By chaining ->with([...]) To avoid Lazy Loading n+1 problem
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employer']);
    }
}
