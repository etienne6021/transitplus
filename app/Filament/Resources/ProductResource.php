<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventes & Distribution';

    protected static ?string $modelLabel = 'Produit';

    protected static ?string $pluralModelLabel = 'Produits & Articles';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fiche Produit')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Désignation')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sku')
                            ->label('Référence / SKU')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Prix d\'achat')
                            ->numeric()
                            ->prefix('FCFA'),
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Prix de vente')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Quantité en Stock')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('min_stock_level')
                            ->label('Seuil d\'alerte stock')
                            ->numeric()
                            ->default(5)
                            ->helperText('Un avertissement apparaîtra quand le stock atteindra ce niveau.'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Désignation')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Prix Vente')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($record) => $record->stock_quantity <= $record->min_stock_level ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                //
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

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('commerce', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
