<?php

namespace App\Filament\Resources\DossierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Trésorerie & Débours';

    protected static ?string $modelLabel = 'Opération';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'Provision' => 'Encaissement (Provision Client)',
                        'Débours' => 'Décaissement (Débours Agence)',
                    ])->required(),
                Forms\Components\TextInput::make('label')
                    ->label('Libellé / Description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Montant')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->label('Mode')
                    ->options([
                        'Espèces' => 'Espèces',
                        'Chèque' => 'Chèque',
                        'Virement' => 'Virement',
                        'Mobile Money' => 'T-Money / Flooz',
                    ])->required()
                    ->default('Espèces'),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => $state === 'Provision' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('label')
                    ->label('Description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Mode')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->money('XOF')->label('Total'),
                    ]),
            ])
            ->defaultSort('date', 'desc')
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
