<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrolls';

    protected static ?string $title = 'Historique des Bulletins de Paie';

    protected static ?string $modelLabel = 'Bulletin de Paie';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('Gains')
                            ->schema([
                                Forms\Components\TextInput::make('base_salary')
                                    ->label('Salaire de Base')
                                    ->numeric()
                                    ->required()
                                    ->default(fn ($livewire) => $livewire->getOwnerRecord()->salary)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('bonuses')
                                    ->label('Primes')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('transport_allowance')
                                    ->label('Transport')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                            ]),
                        Forms\Components\Section::make('Retenues')
                            ->schema([
                                Forms\Components\TextInput::make('irpp')
                                    ->label('IRPP')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                                Forms\Components\TextInput::make('deductions')
                                    ->label('Autres Retenues')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => static::updateTotals($set, $get)),
                            ]),
                    ]),

                Forms\Components\Section::make('Résultat')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('net_salary')
                                    ->label('NET À PAYER')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('FCFA')
                                    ->extraInputAttributes(['class' => 'font-bold text-success-600']),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'Brouillon' => 'Brouillon',
                                        'Payé' => 'Payé',
                                    ])->default('Brouillon'),
                            ]),
                    ]),
            ]);
    }

    public static function updateTotals(Forms\Set $set, Forms\Get $get)
    {
        $base = (float) $get('base_salary') ?? 0;
        $bonuses = (float) $get('bonuses') ?? 0;
        $transport = (float) $get('transport_allowance') ?? 0;
        
        $brut = $base + $bonuses + $transport;
        
        $cnss_employee = round($brut * 0.04);
        $deductions = (float) $get('deductions') ?? 0;
        $irpp = (float) $get('irpp') ?? 0;
        
        $net = $brut - $cnss_employee - $deductions - $irpp;
        
        $set('brut_salary', $brut);
        $set('cnss_employee', $cnss_employee);
        $set('net_salary', $net);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('month')
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Période')
                    ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::create(null, $state)->translatedFormat('F') . ' ' . $record->year),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Net à Payer')
                    ->money('XOF')
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
