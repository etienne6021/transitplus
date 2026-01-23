<?php

namespace App\Filament\Widgets;

use App\Models\MailRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UrgentMailWidget extends BaseWidget
{
    protected static ?string $heading = 'Courriers Urgents à Traiter';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MailRecord::query()->where('statut', 'Urgent')
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Objet')
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->url(fn (MailRecord $record): string => \App\Filament\Resources\MailRecordResource::getUrl('index', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
