<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StockAlerts extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '⚠️ Alertes de Stock Bas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->whereColumn('stock_quantity', '<=', 'min_stock_level')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Produit')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('Réf'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock Actuel')
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('min_stock_level')
                    ->label('Seuil Alerte'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Etat')
                    ->badge()
                    ->state(fn ($record) => $record->stock_quantity <= 0 ? 'Rupture' : 'Bas')
                    ->color(fn ($state) => $state === 'Rupture' ? 'danger' : 'warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Ajuster')
                    ->url(fn (Product $record) => \App\Filament\Resources\ProductResource::getUrl('index', ['tableSearch' => $record->name])),
            ]);
    }
}
