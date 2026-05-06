<?php

namespace App\Filament\Resources\EmployeeEmployments;

use App\Filament\Exports\EmployeeEmploymentExporter;
use App\Filament\Resources\EmployeeEmployments\Pages\ManageEmployeeEmployments;
use App\Models\EmployeeEmployment;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeEmploymentResource extends Resource
{
    protected static ?string $model = EmployeeEmployment::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'job_title';

    protected static ?string $navigationLabel = 'Employment';
    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('employee_id')
                    ->default(1)
                    ->dehydrated()
                    ->required(1)
                    ->columnSpanFull(),

                Section::make('Employment Details')
                    ->schema([
                        TextInput::make('job_title'),
                        TextInput::make('postcode')->label('Workplace Postcode'),
                        DatePicker::make('start_date'),
                        DatePicker::make('continuous_start_date'),
                    ])->columns(2)->columnSpan(2),

                Section::make('Name')
                    ->schema([
                        TextInput::make('payroll_code'),

                        Select::make('declaration')
                            ->label('Declaration')
                            ->options(
                                collect(config('general.employment_declaration'))
                                    ->mapWithKeys(fn ($text, $key) => [
                                        $key => "$key <span style='font-size:12px; color:#6b7280; margin-right:6px;'>$text</span>",
                                    ])
                                    ->toArray()
                            )
                            ->allowHtml()
                            ->native(false)
                            ->searchable(),

                        Select::make('change_of_payroll_id')
                            ->label('Change of Payroll ID')
                            ->options(
                                collect(config('general.employment_change_of_payroll_id'))
                                    ->mapWithKeys(fn ($item, $key) => [$key => $item['label']])
                                    ->toArray()
                            )
                            ->reactive()
                            ->native(false)
                            ->searchable(),

                        TextInput::make('previous_payroll_code')
                            ->label('Previous Payroll Code')
                            ->visible(fn ($get) =>
                                config('general.employment_change_of_payroll_id')[$get('change_of_payroll_id')]['requires_previous_code'] ?? false
                            ),
                    ])->columns(2)->columnSpan(2),

                Section::make('Settings')->schema([
                    Toggle::make('exclude_from_pay_runs'),
                    Toggle::make('works_in_freeport'),
                    Toggle::make('works_in_investment_zone'),
                ])->columnSpanFull(),

                Section::make('Pension')->schema([
                    DatePicker::make('pension_payroll_start_date'),
                    TextInput::make('annual_pension_amount'),
                    DatePicker::make('leave_date'),
                ])->columns(3)->columnSpan(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('job_title')
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_title')
                    ->searchable(),
                TextColumn::make('postcode')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('continuous_start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payroll_code')
                    ->searchable(),
                TextColumn::make('declaration')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('change_of_payroll_id')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('exclude_from_pay_runs')
                    ->boolean(),
                TextColumn::make('pension_payroll_start_date')
                    ->date()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('annual_pension_amount')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('works_in_freeport')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('works_in_investment_zone')
                    ->boolean()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('leave_date')
                    ->date()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
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
                        ->exporter(EmployeeEmploymentExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployeeEmployments::route('/'),
        ];
    }

    private static function declarationOptionLabel(string $text): string
    {
        return "<strong>{$text}</strong>";
    }
}
