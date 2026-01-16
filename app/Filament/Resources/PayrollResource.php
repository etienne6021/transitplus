<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Ressources Humaines';

    protected static ?string $modelLabel = 'Bulletin de Paie';

    protected static ?string $pluralModelLabel = 'Paie';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Période & Employé')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->label('Employé')
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $employee = \App\Models\Employee::find($state);
                                if ($employee) {
                                    $set('base_salary', $employee->salary);
                                    
                                    // Recalcul manuel initial
                                    $brut = (float) $employee->salary;
                                    $cnss_employee = round($brut * 0.04);
                                    $cnss_employer = round($brut * 0.175);
                                    $net = $brut - $cnss_employee;

                                    $set('brut_salary', $brut);
                                    $set('cnss_employee', $cnss_employee);
                                    $set('cnss_employer', $cnss_employer);
                                    $set('net_salary', $net);
                                }
                            }),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Mois')
                                    ->options([
                                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                    ])->required()
                                    ->default(date('n')),
                                Forms\Components\TextInput::make('year')
                                    ->label('Année')
                                    ->numeric()
                                    ->default(date('Y'))
                                    ->required(),
                            ]),
                    ])->columns(2),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('Gains (Revenus Brut)')
                            ->schema([
                                Forms\Components\TextInput::make('base_salary')
                                    ->label('Salaire de Base')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('bonuses')
                                    ->label('Primes de rendement')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('transport_allowance')
                                    ->label('Indemnité de Transport')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('other_allowances')
                                    ->label('Autres Indemnités')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                            ]),

                        Forms\Components\Section::make('Retenues & Charges Sociales')
                            ->schema([
                                Forms\Components\TextInput::make('cnss_employee')
                                    ->label('CNSS Ouvrière (4%)')
                                    ->numeric()
                                    ->readOnly()
                                    ->helperText('Part employé (4% du brut)'),
                                Forms\Components\TextInput::make('cnss_employer')
                                    ->label('CNSS Patronale (17.5%)')
                                    ->numeric()
                                    ->readOnly()
                                    ->helperText('Part employeur (17.5% du brut)'),
                                Forms\Components\TextInput::make('irpp')
                                    ->label('IRPP (Impôt)')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('deductions')
                                    ->label('Autres Retenues (Avances, etc.)')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                            ]),
                    ]),

                Forms\Components\Section::make('Synthèse Financière')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('brut_salary')
                                    ->label('Salaire Brut Total')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('FCFA'),
                                Forms\Components\TextInput::make('net_salary')
                                    ->label('NET À PAYER')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('FCFA')
                                    ->extraInputAttributes(['class' => 'font-bold text-lg text-success-600']),
                                Forms\Components\Select::make('status')
                                    ->label('Statut du paiement')
                                    ->options([
                                        'Brouillon' => 'Brouillon',
                                        'Payé' => '✅ Payé / Virement effectué',
                                    ])->default('Brouillon'),
                            ]),
                    ]),
            ]);
    }

    public static function updateTotals(Forms\Set $set, Forms\Get $get)
    {
        $base = (float) ($get('base_salary') ?? 0);
        $bonuses = (float) ($get('bonuses') ?? 0);
        $transport = (float) ($get('transport_allowance') ?? 0);
        $others = (float) ($get('other_allowances') ?? 0);
        
        $brut = $base + $bonuses + $transport + $others;
        
        $cnss_employee = round($brut * 0.04);
        $cnss_employer = round($brut * 0.175);
        
        $deductions = (float) ($get('deductions') ?? 0);
        $irpp = (float) ($get('irpp') ?? 0);
        
        $net = $brut - $cnss_employee - $deductions - $irpp;
        
        $set('brut_salary', $brut);
        $set('cnss_employee', $cnss_employee);
        $set('cnss_employer', $cnss_employer);
        $set('net_salary', $net);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->state(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('month')
                    ->label('Période')
                    ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::create(null, $state)->translatedFormat('F') . ' ' . $record->year)
                    ->sortable(),
                Tables\Columns\TextColumn::make('brut_salary')
                    ->label('Brut')
                    ->money('XOF')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Net à Payer')
                    ->money('XOF')
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'Payé' ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->relationship('employee', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->label('Filtrer par employé')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('month')
                    ->label('Mois')
                    ->options([
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ]),
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => route('payroll.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('hr', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayrolls::route('/'),
        ];
    }
}
