<?php

namespace App\Filament\Resources\EmployerRtis;

use App\Filament\Exports\RtiExporter;
use App\Filament\Resources\EmployerRtis\Pages\ManageEmployerRtis;
use App\Models\Rti;
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
use UnitEnum;

class EmployerRtiResource extends Resource
{
    protected static ?string $model = Rti::class;

    protected static ?string $recordTitleAttribute = 'period_amount';

    protected static UnitEnum|string|null $navigationGroup = 'Employers';

    protected static ?string $navigationLabel = 'RTI';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Hidden::make('employer_id')
                ->default(fn (): ?int => auth()->id())
                ->dehydrated()
                ->required(fn (): bool => filled(auth()->id())),

                Section::make('Sender Details')
                    ->schema([
                        Select::make('sender_type_id')
                            ->label('Sender Type')
                            ->relationship('senderType', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('govt_gateway_id')->label('Government Gateway ID'),

                        TextInput::make('password')->password()->revealable()->label('Password'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Contact Details')
                    ->schema([
                        TextInput::make('first_name'),
                        TextInput::make('last_name'),
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('RTI Settings')
                    ->schema([
                        Toggle::make('auto_submit_fps_after_finalising_pay_run')
                            ->label(new HtmlString('
                                        Auto Submit
                                        <span style="font-size:12px; color:#6b7280;">
                                            Automatically submit FPS after finalising a Pay Run – more automation
                                        </span>
                                    ')),

                        Toggle::make('include_employees_with_no_payment_on_fps')
                            ->label(new HtmlString('
                                        Exclude Nil Paid
                                        <span style="font-size:12px; color:#6b7280;">
                                            Dont include employees with no payment on the FPS
                                        </span>
                                    ')),

                        Toggle::make('test_mode')
                            ->label(new HtmlString('
                                        Test Mode
                                        <span style="font-size:12px; color:#6b7280;">
                                            Set the Test flag on submissions ("Test in Live")
                                        </span>
                                    ')),

                        Toggle::make('use_test_gateway')
                            ->label(new HtmlString('
                                        Use Test Gateway
                                        <span style="font-size:12px; color:#6b7280;">
                                            Use HMRC Test Transaction Service instead of Live
                                        </span>
                                    ')),

                        Toggle::make('allow_linked_eps')
                            ->label(new HtmlString('
                                        Allow Linked EPS
                                        <span style="font-size:12px; color:#6b7280;">
                                            This will allow you to submit a combined Employer Payment Summary
                                        </span>
                                    ')),

                        Toggle::make('compress_fps')
                            ->label(new HtmlString('
                                        Compress FPS
                                        <span style="font-size:12px; color:#6b7280;">
                                            This allows very large payruns to be submitted to HMRC
                                        </span>
                                    ')),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('govt_gateway_id')
            ->columns([
                // Employer
                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable()
                    ->sortable(),

                // Sender Type
                TextColumn::make('senderType.name')
                    ->label('Sender Type')
                    ->sortable(),

                // Gateway
                TextColumn::make('govt_gateway_id')
                    ->label('Gateway ID')
                    ->searchable(),

                // Contact
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                // Toggles (as icons)
                IconColumn::make('auto_submit_fps_after_finalising_pay_run')
                    ->label('Auto FPS')
                    ->boolean(),

                IconColumn::make('test_mode')
                    ->label('Test')
                    ->boolean(),

                IconColumn::make('use_test_gateway')
                    ->label('Test Gateway')
                    ->boolean(),

                // Dates (hidden by default)
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->label('Download Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(RtiExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ]),
                ]),
            ]);
    }

    /**
     * @return Builder
     * When renders a table it uses getEloquentQuery() as the base query for fetching all rows. By chaining ->with([...]) To avoid Lazy Loading n+1 problem
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employer', 'senderType']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployerRtis::route('/'),
        ];
    }
}
