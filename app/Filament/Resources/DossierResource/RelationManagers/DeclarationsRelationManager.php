<?php

namespace App\Filament\Resources\DossierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeclarationsRelationManager extends RelationManager
{
    protected static string $relationship = 'declarations';

    protected static ?string $title = 'Suivi Douanier (Sydonia)';

    protected static ?string $modelLabel = 'Déclaration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('numero_sydonia')
                        ->required()
                        ->label('N° Déclaration (Liasse)'),
                    Forms\Components\DatePicker::make('date_sydonia')
                        ->label('Date Liquidation')
                        ->required()
                        ->default(now()),
                ])->columns(2),
                Forms\Components\Group::make([
                    Forms\Components\Select::make('bureau')
                        ->options(fn() => \App\Models\CustomsOffice::all()->pluck('name', 'code'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('regime')
                        ->options(fn() => \App\Models\CustomsRegime::all()->pluck('label', 'code'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('circuit')
                        ->options([
                            'Vert' => 'Circuit Vert',
                            'Jaune' => 'Circuit Jaune',
                            'Bleu' => 'Circuit Bleu',
                            'Rouge' => 'Circuit Rouge',
                        ])->required(),
                ])->columns(3),
                Forms\Components\TextInput::make('droits_douane')
                    ->label('Montant Droits Payés')
                    ->numeric()
                    ->prefix('FCFA')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_sydonia')
            ->columns([
                Tables\Columns\TextColumn::make('numero_sydonia')
                    ->label('N° Liasse')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_sydonia')
                    ->label('Date')
                    ->date(),
                Tables\Columns\TextColumn::make('circuit')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Vert' => 'success',
                        'Jaune' => 'warning',
                        'Rouge' => 'danger',
                        'Bleu' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('regime')
                    ->label('Régime'),
                Tables\Columns\TextColumn::make('droits_douane')
                    ->label('Droits')
                    ->money('XOF')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('XOF')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
