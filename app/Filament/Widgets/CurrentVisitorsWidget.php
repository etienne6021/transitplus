<?php

namespace App\Filament\Widgets;

use App\Models\VisitorRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Log;

class CurrentVisitorsWidget extends BaseWidget
{
    protected static ?string $heading = '👥 Visiteurs Actuellement Présents';
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
                Tables\Columns\TextColumn::make('person_to_see')
                    ->label('Reçu par'),
                Tables\Columns\TextColumn::make('entry_time')
                    ->label('Arrivée')
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
