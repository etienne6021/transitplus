<?php

namespace App\Filament\Widgets;

use App\Models\VisitorRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CurrentVisitorsWidget extends BaseWidget
{
    protected static ?string $heading = 'Visiteurs Actuellement en Séance';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VisitorRecord::query()->whereNull('exit_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('Visiteur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitedUser.name')
                    ->label('Reçu par'),
                Tables\Columns\TextColumn::make('entry_time')
                    ->label('Entrée')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('sign_out')
                    ->label('Sortie')
                    ->icon('heroicon-o-clock')
                    ->color('success')
                    ->action(fn (VisitorRecord $record) => $record->update(['exit_time' => now()])),
            ]);
    }
}
