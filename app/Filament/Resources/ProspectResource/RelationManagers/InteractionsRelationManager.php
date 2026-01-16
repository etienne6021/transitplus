<?php

namespace App\Filament\Resources\ProspectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'Appel' => 'Appel Téléphonique',
                        'Visite' => 'Visite Terrain',
                        'Meeting' => 'Rendez-vous Bureau',
                        'WhatsApp' => 'Échange WhatsApp',
                        'Email' => 'Email',
                    ])->required(),
                Forms\Components\DateTimePicker::make('performed_at')
                    ->label('Date & Heure')
                    ->default(now())
                    ->required(),
                Forms\Components\RichEditor::make('content')
                    ->label('Détails de l\'échange')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'WhatsApp' => 'success',
                        'Appel' => 'info',
                        'Visite' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('Notes')
                    ->html()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle Interaction'),
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
