<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Trésorerie & Finance';

    protected static ?string $modelLabel = 'Facture Client';

    protected static ?string $pluralModelLabel = 'Factures Clients';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('En-tête de Facture')
                    ->schema([
                        Forms\Components\Select::make('dossier_id')
                            ->relationship('dossier', 'reference')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('number')
                            ->label('Numéro de facture')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(function() {
                                $year = date('Y');
                                $lastInvoice = \App\Models\Invoice::where('number', 'like', "FAC-{$year}-%")
                                    ->orderByRaw('CAST(SUBSTR(number, -4) AS UNSIGNED) DESC')
                                    ->first();
                                
                                if ($lastInvoice) {
                                    $lastNum = intval(substr($lastInvoice->number, -4));
                                    $nextNum = $lastNum + 1;
                                } else {
                                    $nextNum = 1;
                                }
                                
                                return 'FAC-' . $year . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                            })
                            ->helperText('Généré automatiquement, mais modifiable si nécessaire.'),
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Date d\'échéance')
                            ->default(now()->addDays(15)),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Brouillon' => 'Brouillon',
                                'Envoyée' => 'Envoyée',
                                'Payée' => 'Payée',
                                'Annulée' => 'Annulée',
                            ])->default('Brouillon')->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Lignes de Facture')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Montant')
                                    ->numeric()
                                    ->required()
                                    ->prefix('FCFA')
                                    ->live(onBlur: true),
                                Forms\Components\Toggle::make('is_debours')
                                    ->label('Débours (sans TVA)')
                                    ->default(false)
                                    ->live(),
                            ])->columns(4)
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $items = $get('items') ?? [];
                                $subHonoraires = 0;
                                $subDebours = 0;

                                foreach ($items as $item) {
                                    $amt = floatval($item['amount'] ?? 0);
                                    if (!empty($item['is_debours'])) {
                                        $subDebours += $amt;
                                    } else {
                                        $subHonoraires += $amt;
                                    }
                                }

                                $tax = $subHonoraires * 0.18; // TVA 18% au Togo
                                
                                $set('subtotal_honoraires', $subHonoraires);
                                $set('subtotal_debours', $subDebours);
                                $set('tax_amount', $tax);
                                $set('total_amount', $subHonoraires + $subDebours + $tax);
                            }),
                    ]),

                Forms\Components\Section::make('Récapitulatif Financier')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal_honoraires')
                            ->label('Total Honoraires')
                            ->numeric()
                            ->readOnly()
                            ->prefix('FCFA'),
                        Forms\Components\TextInput::make('subtotal_debours')
                            ->label('Total Débours')
                            ->numeric()
                            ->readOnly()
                            ->prefix('FCFA'),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('TVA (18%)')
                            ->numeric()
                            ->readOnly()
                            ->prefix('FCFA'),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('TOTAL À PAYER')
                            ->numeric()
                            ->readOnly()
                            ->prefix('FCFA'),
                    ])->columns(4),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
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
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('dossier.client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dossier.reference')
                    ->label('Dossier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Montant TTC')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' FCFA'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Brouillon' => 'gray',
                        'Envoyée' => 'warning',
                        'Payée' => 'success',
                        'Annulée' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Imprimer PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Invoice $record) => route('invoice.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('transit', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
