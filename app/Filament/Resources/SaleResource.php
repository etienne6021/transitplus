<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventes & Distribution';

    protected static ?string $modelLabel = 'Vente';

    protected static ?string $pluralModelLabel = 'Ventes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de la Vente')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('N° Facture Vente')
                            ->required()
                            ->default('FAC-' . date('YmdHis'))
                            ->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('date')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->label('Client')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Articles Vendus')
                    ->headerActions([
                        Forms\Components\Actions\Action::make('check_stock')
                            ->label('Vérifier Stocks')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(fn () => \Filament\Notifications\Notification::make()->title('Stock vérifié')->success()->send()),
                    ])
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produit')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->sale_price);
                                            $set('total_price', $product->sale_price);
                                        }
                                    })
                                    ->helperText(fn (Forms\Get $get) => 
                                        $get('product_id') ? "Stock actuel: " . \App\Models\Product::find($get('product_id'))?->stock_quantity : null
                                    )
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qté')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                $set('total_price', ($state * $get('unit_price')) - $get('discount'));
                                                
                                                $product = \App\Models\Product::find($get('product_id'));
                                                if ($product && $state > $product->stock_quantity) {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Stock Insuffisant')
                                                        ->body("Attention: la quantité demandée ({$state}) est supérieure au stock disponible ({$product->stock_quantity}).")
                                                        ->warning()
                                                        ->send();
                                                }
                                            }),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Prix Unit.')
                                            ->numeric()
                                            ->required()
                                            ->prefix('FCFA')
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                                $set('total_price', ($state * $get('quantity')) - $get('discount'))
                                            ),

                                        Forms\Components\TextInput::make('discount')
                                            ->label('Remise')
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('FCFA')
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                                $set('total_price', ($get('quantity') * $get('unit_price')) - $state)
                                            ),

                                        Forms\Components\TextInput::make('total_price')
                                            ->label('Total Ligne')
                                            ->numeric()
                                            ->readonly()
                                            ->prefix('FCFA'),
                                    ]),
                            ])
                            ->live()
                            ->itemLabel(fn (array $state): ?string => (\App\Models\Product::find($state['product_id'] ?? null)?->name ?? 'Nouvel article'))
                            ->defaultItems(1)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->columnSpanFull()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $subtotal = collect($get('items'))->sum('total_price');
                                $set('subtotal', $subtotal);
                                
                                $discount = (float) $get('discount_amount');
                                $hasTax = (bool) $get('has_tax');
                                $taxAmount = $hasTax ? ($subtotal - $discount) * 0.18 : 0;
                                
                                $set('tax_amount', $taxAmount);
                                $set('total_amount', ($subtotal - $discount) + $taxAmount);
                            }),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Calcul des Taxes')
                            ->schema([
                                Forms\Components\Toggle::make('has_tax')
                                    ->label('Appliquer la TVA (18%)')
                                    ->default(true)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        $subtotal = (float) $get('subtotal');
                                        $discount = (float) $get('discount_amount');
                                        $hasTax = (bool) $get('has_tax');
                                        $taxAmount = $hasTax ? ($subtotal - $discount) * 0.18 : 0;
                                        $set('tax_amount', $taxAmount);
                                        $set('total_amount', ($subtotal - $discount) + $taxAmount);
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Sous-total HT')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA'),
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Remise Globale')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('FCFA')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        $subtotal = (float) $get('subtotal');
                                        $discount = (float) $get('discount_amount');
                                        $hasTax = (bool) $get('has_tax');
                                        $taxAmount = $hasTax ? ($subtotal - $discount) * 0.18 : 0;
                                        $set('tax_amount', $taxAmount);
                                        $set('total_amount', ($subtotal - $discount) + $taxAmount);
                                    }),
                                Forms\Components\TextInput::make('tax_amount')
                                    ->label('TVA (18%)')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA')
                                    ->visible(fn (Forms\Get $get) => $get('has_tax')),
                            ])->columnSpan(1),
                        
                        Forms\Components\Section::make('Statut & Net à Payer')
                            ->schema([
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Net à Payer')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA')
                                    ->extraInputAttributes(['class' => 'text-xl font-bold text-success-600']),
                                Forms\Components\Select::make('payment_status')
                                    ->label('Statut Paiement')
                                    ->options([
                                        'En attente' => 'En attente',
                                        'Partiel' => 'Partiel',
                                        'Payé' => 'Payé',
                                    ])->default('En attente'),
                                Forms\Components\Select::make('delivery_status')
                                    ->label('Statut Livraison')
                                    ->options([
                                        'En attente' => 'En attente',
                                        'Livré' => 'Livré',
                                    ])->default('En attente'),
                            ])->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° Facture')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Net à Payer')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Payé')
                    ->money('XOF')
                    ->color('success'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Reste')
                    ->money('XOF')
                    ->state(fn ($record) => $record->total_amount - $record->paid_amount)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Paiement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En attente' => 'gray',
                        'Partiel' => 'warning',
                        'Payé' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('delivery_status')
                    ->label('Livraison')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En attente' => 'warning',
                        'Livré' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Statut Paiement')
                    ->options([
                        'En attente' => 'En attente',
                        'Partiel' => 'Partiel',
                        'Payé' => 'Payé',
                    ]),
                Tables\Filters\SelectFilter::make('delivery_status')
                    ->label('Statut Livraison')
                    ->options([
                        'En attente' => 'En attente',
                        'Livré' => 'Livré',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Sale $record) => route('sale.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('recordPayment')
                    ->label('Encaisser')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant encaissé')
                            ->numeric()
                            ->required()
                            ->default(fn ($record) => $record->total_amount - $record->paid_amount),
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode')
                            ->options([
                                'Espèces' => 'Espèces',
                                'Chèque' => 'Chèque',
                                'Virement' => 'Virement',
                                'Mobile Money' => 'T-Money / Flooz',
                            ])->required()
                            ->live(),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('N° Chèque / Réf')
                            ->visible(fn (Forms\Get $get) => in_array($get('payment_method'), ['Chèque', 'Virement', 'Mobile Money'])),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Banque')
                            ->visible(fn (Forms\Get $get) => in_array($get('payment_method'), ['Chèque', 'Virement'])),
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Scan Justificatif')
                            ->directory('accounting/sales'),
                    ])
                    ->action(function ($record, array $data) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                            $record->increment('paid_amount', $data['amount']);
                            
                            $record->update([
                                'payment_status' => $record->paid_amount >= $record->total_amount ? 'Payé' : 'Partiel'
                            ]);

                            \App\Models\FinancialTransaction::create([
                                'sale_id' => $record->id,
                                'agency_id' => $record->agency_id,
                                'direction' => 'Entrée',
                                'type' => 'Vente',
                                'category' => 'Commerce',
                                'label' => 'Encaissement Facture ' . $record->number,
                                'amount' => $data['amount'],
                                'date' => now(),
                                'payment_method' => $data['payment_method'],
                                'reference_number' => $data['reference_number'] ?? null,
                                'bank_name' => $data['bank_name'] ?? null,
                                'attachment' => $data['attachment'] ?? null,
                            ]);
                        });
                        \Filament\Notifications\Notification::make()->title('Encaissement réussi')->success()->send();
                    })
                    ->visible(fn ($record) => $record->payment_status !== 'Payé'),

                Tables\Actions\Action::make('deliver')
                    ->label('Livrer')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['delivery_status' => 'Livré']))
                    ->visible(fn ($record) => $record->delivery_status !== 'Livré'),

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
            'index' => Pages\ManageSales::route('/'),
        ];
    }
}
