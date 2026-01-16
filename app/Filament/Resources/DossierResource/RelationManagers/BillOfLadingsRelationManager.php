<?php

namespace App\Filament\Resources\DossierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillOfLadingsRelationManager extends RelationManager
{
    protected static string $relationship = 'billOfLadings';

    protected static ?string $title = 'Logistique (BL & Navire)';

    protected static ?string $modelLabel = 'Connaissement';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('N° de BL')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('ship_id')
                    ->relationship('ship', 'name')
                    ->label('Navire')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('etd')
                    ->label('ETD'),
                Forms\Components\DatePicker::make('eta')
                    ->label('ETA'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° BL')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('ship.name')
                    ->label('Navire'),
                Tables\Columns\TextColumn::make('eta')
                    ->label('ETA')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Ajouter BL'),
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
