<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Ventes & Distribution';

    protected static ?string $modelLabel = 'Devis';

    protected static ?string $pluralModelLabel = 'Devis';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du Devis / Proforma')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('N° de Devis')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('date')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('prospect_id')
                            ->relationship('prospect', 'name')
                            ->label('Prospect')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => !$get('client_id'))
                            ->visible(fn (Forms\Get $get) => !$get('client_id')),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->label('Client Existant')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => !$get('prospect_id'))
                            ->visible(fn (Forms\Get $get) => !$get('prospect_id')),
                    ])->columns(2),

                Forms\Components\Section::make('Lignes du Devis / Proforma')
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
                                                        ->title('Stock Insuffisant (Alerte)')
                                                        ->body("Attention: la quantité devisée ({$state}) dépasse le stock actuel ({$product->stock_quantity}).")
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
                                $taxRate = (float) $get('tax_rate');
                                $hasTax = (bool) $get('has_tax');
                                
                                $taxableAmount = $subtotal - $discount;
                                $taxAmount = $hasTax ? ($taxableAmount * ($taxRate / 100)) : 0;
                                
                                $set('tax_amount', $taxAmount);
                                $set('total_amount', $taxableAmount + $taxAmount);
                            }),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Récapitulatif & Taxes')
                            ->schema([
                                Forms\Components\Toggle::make('has_tax')
                                    ->label('Appliquer TVA (18%)')
                                    ->default(true)
                                    ->live(),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('Taux TVA (%)')
                                    ->numeric()
                                    ->default(18.0)
                                    ->live()
                                    ->visible(fn ($get) => $get('has_tax')),
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Remise Globale')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('FCFA')
                                    ->live(),
                            ])->columnSpan(1),
                        
                        Forms\Components\Section::make('Totaux')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Sous-total HT')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA'),
                                Forms\Components\TextInput::make('tax_amount')
                                    ->label('Montant TVA')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA'),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('NET À PAYER')
                                    ->numeric()
                                    ->readonly()
                                    ->prefix('FCFA')
                                    ->extraInputAttributes(['class' => 'text-xl font-bold text-primary-600']),
                            ])->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° Devis')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('prospect.name')
                    ->label('Cible')
                    ->searchable()
                    ->state(fn ($record) => $record->prospect?->name ?? $record->client?->name),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('XOF'),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Payé')
                    ->money('XOF')
                    ->color('success'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Reste')
                    ->money('XOF')
                    ->state(fn ($record) => $record->total_amount - $record->paid_amount)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Brouillon' => 'gray',
                        'Envoyé' => 'info',
                        'Accepté' => 'success',
                        'Refusé' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Imprimer Proforma')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Quotation $record) => route('quotation.pdf', $record))
                    ->openUrlInNewTab(),
                
                Tables\Actions\Action::make('convertToSale')
                    ->label('Convertir en Vente')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Générer la Facture de Vente ?')
                    ->modalDescription('Cela créera une facture officielle, déduira les stocks et permettra l\'encaissement.')
                    ->action(function (Quotation $record) {
                        if (!$record->client_id) {
                            \Filament\Notifications\Notification::make()
                                ->title('Veuillez d\'abord convertir le prospect en client.')
                                ->danger()->send();
                            return;
                        }

                        DB::transaction(function () use ($record) {
                            // 1. Create Sale
                            $sale = \App\Models\Sale::create([
                                'agency_id' => $record->agency_id,
                                'client_id' => $record->client_id,
                                'quotation_id' => $record->id,
                                'number' => 'FAC-' . $record->number,
                                'date' => now(),
                                'has_tax' => $record->has_tax,
                                'subtotal' => $record->subtotal,
                                'tax_amount' => $record->tax_amount,
                                'discount_amount' => $record->discount_amount,
                                'total_amount' => $record->total_amount,
                                'payment_status' => 'En attente',
                                'delivery_status' => 'En attente',
                            ]);

                            // 2. Create Sale Items & Decrement Stock
                            foreach ($record->items as $item) {
                                \App\Models\SaleItem::create([
                                    'sale_id' => $sale->id,
                                    'product_id' => $item->product_id,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'discount' => $item->discount,
                                    'total_price' => $item->total_price,
                                ]);
                            }

                            // 3. Update Quotation
                            $record->update(['status' => 'Accepté']);

                            return redirect()->to(\App\Filament\Resources\SaleResource::getUrl('index'));
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Vente générée avec succès !')
                            ->success()->send();
                    })
                    ->visible(fn (Quotation $record) => $record->status !== 'Accepté' && $record->client_id !== null),

                Tables\Actions\Action::make('recordPayment')
                    ->label('Encaisser')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant à encaisser')
                            ->numeric()
                            ->required()
                            ->default(fn (Quotation $record) => $record->total_amount - $record->paid_amount),
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de Paiement')
                            ->options([
                                'Espèces' => 'Espèces',
                                'Chèque' => 'Chèque',
                                'Virement' => 'Virement',
                                'Mobile Money' => 'T-Money / Flooz',
                            ])->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date d\'encaissement')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (Quotation $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->increment('paid_amount', $data['amount']);
                            
                            \App\Models\FinancialTransaction::create([
                                'quotation_id' => $record->id,
                                'agency_id' => $record->agency_id,
                                'type' => 'Vente',
                                'category' => 'Commerce',
                                'label' => 'Encaissement Vente ' . $record->number,
                                'amount' => $data['amount'],
                                'date' => $data['date'],
                                'payment_method' => $data['payment_method'],
                            ]);
                        });

                        \Filament\Notifications\Notification::make()->title('Encaissement enregistré !')->success()->send();
                    })
                    ->visible(fn (Quotation $record) => $record->status === 'Accepté' && ($record->total_amount > $record->paid_amount)),

                Tables\Actions\Action::make('createDossier')
                    ->label('Ouvrir Dossier')
                    ->icon('heroicon-o-folder-plus')
                    ->color('info') // Changed color from 'success' to 'info'
                    ->requiresConfirmation()
                    ->modalHeading('Ouvrir un dossier de transit ?')
                    ->modalDescription('Cela créera un nouveau dossier de transit pour ce client à partir du devis.')
                    ->action(function (Quotation $record) {
                        if (!$record->client_id) {
                            \Filament\Notifications\Notification::make()
                                ->title('Veuillez d\'abord convertir le prospect en client.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $dossier = \App\Models\Dossier::create([
                            'client_id' => $record->client_id,
                            'agency_id' => $record->agency_id,
                            'reference' => 'D-' . $record->number,
                            'type' => 'Import', // Par défaut
                            'mode' => 'Maritime', // Par défaut
                            'statut' => 'Ouvert',
                        ]);

                        $record->update(['status' => 'Accepté']);

                        \Filament\Notifications\Notification::make()
                            ->title('Dossier ouvert avec succès !')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Quotation $record) => $record->status === 'Accepté' || $record->status === 'Envoyé'),
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
            'index' => Pages\ManageQuotations::route('/'),
        ];
    }
}
