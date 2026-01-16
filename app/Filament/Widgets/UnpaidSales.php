<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UnpaidSales extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '🚚 Ventes Livrées Non Payées';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->where('delivery_status', 'Livré')
                    ->where('payment_status', '!=', 'Payé')
            )
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° Facture')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('XOF'),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Payé')
                    ->money('XOF'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Reste à recouvrer')
                    ->state(fn ($record) => $record->total_amount - $record->paid_amount)
                    ->money('XOF')
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir la vente')
                    ->url(fn (Sale $record) => \App\Filament\Resources\SaleResource::getUrl('index', ['tableSearch' => $record->number])),
            ]);
    }
}
