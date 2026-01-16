<?php

namespace App\Filament\Resources\DossierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntreesRelationManager extends RelationManager
{
    protected static string $relationship = 'entrees';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference_mad')
                    ->label('Réf. MAD')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_arrivee')
                    ->label('Arrivée')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('provenance')
                    ->required(),
                Forms\Components\TextInput::make('nombre_colis')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('statut')
                    ->options([
                        'En attente' => 'En attente',
                        'Reçu' => 'Reçu',
                        'Sorti' => 'Sorti',
                    ])->default('En attente'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference_mad')
            ->columns([
                Tables\Columns\TextColumn::make('reference_mad')
                    ->label('Réf. MAD')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('date_arrivee')
                    ->date()
                    ->label('Arrivée'),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'En attente' => 'warning',
                        'Reçu' => 'success',
                        'Sorti' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nombre_colis')
                    ->label('Colis'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Enregistrer Arrivée'),
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
