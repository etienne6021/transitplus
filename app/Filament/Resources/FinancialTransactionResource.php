<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialTransactionResource\Pages;
use App\Filament\Resources\FinancialTransactionResource\RelationManagers;
use App\Models\FinancialTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancialTransactionResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finances & Ventes';

    protected static ?string $modelLabel = 'Écriture Comptable';

    protected static ?string $pluralModelLabel = 'Journal de Caisse & Opérations';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'opération')
                    ->schema([
                        Forms\Components\Select::make('direction')
                            ->label('Direction du flux')
                            ->options([
                                'Entrée' => '📥 Entrée (Recette / Encaissement)',
                                'Sortie' => '📤 Sortie (Dépense / Décaissement)',
                            ])->required()
                            ->default('Entrée')
                            ->live(),
                        Forms\Components\Select::make('dossier_id')
                            ->relationship('dossier', 'reference')
                            ->label('Dossier Transit (Optionnel)')
                            ->searchable()
                            ->preload()
                            ->hint('Lier à un dossier spécifique'),
                        Forms\Components\Select::make('type')
                            ->label('Type d\'opération')
                            ->options([
                                'Provision' => 'Provision (Avance client)',
                                'Débours' => 'Débours (Frais payés)',
                                'Honoraire' => 'Honoraires de transit',
                                'Vente' => 'Vente Marchandise',
                                'Créance' => 'Créance (Dû par tiers)',
                                'Dette' => 'Dette (À payer au tiers)',
                                'Audit' => 'Ajustement de caisse',
                            ])->required()
                            ->live(),
                        Forms\Components\Select::make('category')
                            ->label('Catégorie / Entité')
                            ->options([
                                'OTR' => 'Douane (OTR)',
                                'PAL' => 'Port Autonome de Lomé',
                                'LCT' => 'Lomé Container Terminal',
                                'TBS' => 'Togo Terminal (Bolloré)',
                                'Commerce' => 'Commerce / Vente',
                                'Logistique' => 'Transport / Camion',
                                'Personnel' => 'Salaire / RH',
                                'Divers' => 'Autres frais',
                            ]),
                        Forms\Components\TextInput::make('party_name')
                            ->label('Tiers concerné')
                            ->placeholder('Nom du fournisseur ou transporteur'),
                        Forms\Components\TextInput::make('label')
                            ->label('Libellé de l\'opération')
                            ->required()
                            ->placeholder('Ex: Paiement manutention LCT'),
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date d\'opération')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Date d\'échéance')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['Créance', 'Dette'])),
                        Forms\Components\Select::make('status')
                            ->options([
                                'En attente' => 'En attente',
                                'Complété' => 'Complété',
                                'Annulé' => 'Annulé',
                            ])->default('Complété'),
                    ])->columns(2),

                Forms\Components\Section::make('Règlement & Pièces jointes')
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de règlement')
                            ->options([
                                'Espèces' => '💵 Espèces (Caisse)',
                                'Chèque' => '✍️ Chèque',
                                'Virement' => '🏦 Virement / Versement',
                                'Mobile Money' => '📱 Mobile Money (Flooz/TMoney)',
                            ])->live(),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Référence Document')
                            ->placeholder('N° du chèque ou réf virement')
                            ->visible(fn (Forms\Get $get) => in_array($get('payment_method'), ['Chèque', 'Virement', 'Mobile Money'])),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Banque / Institution')
                            ->placeholder('Ex: Ecobank, Orabank...')
                            ->visible(fn (Forms\Get $get) => in_array($get('payment_method'), ['Chèque', 'Virement'])),
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Justificatif (Scan/Photo)')
                            ->directory('accounting/claims')
                            ->image()
                            ->previewable(true)
                            ->openable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observations / Commentaires')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Flux')
                    ->badge()
                    ->color(fn ($state) => $state === 'Entrée' ? 'success' : 'danger')
                    ->icon(fn ($state) => $state === 'Entrée' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'),
                Tables\Columns\TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable()
                    ->description(fn ($record) => $record->party_name),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable()
                    ->color(fn ($record) => $record->direction === 'Entrée' ? 'success' : 'danger')
                    ->weight('bold')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Solde Période')
                            ->money('XOF')
                            ->formatStateUsing(function ($state, $query) {
                                $entries = (clone $query)->where('direction', 'Entrée')->sum('amount');
                                $exits = (clone $query)->where('direction', 'Sortie')->sum('amount');
                                return $entries - $exits;
                            }),
                    ]),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Règlement')
                    ->toggleable()
                    ->description(fn ($record) => $record->reference_number),
                Tables\Columns\IconColumn::make('attachment')
                    ->label('PJ')
                    ->icon('heroicon-o-paper-clip')
                    ->color('info')
                    ->url(fn ($record) => $record && $record->attachment ? asset('storage/' . $record->attachment) : null)
                    ->openUrlInNewTab()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('direction')
                    ->label('Filtrer par flux')
                    ->options([
                        'Entrée' => '📥 Entrées (Recettes)',
                        'Sortie' => '📤 Sorties (Dépenses)',
                    ]),
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
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
        return $agency && is_array($agency->modules) && in_array('finance', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialTransactions::route('/'),
            'create' => Pages\CreateFinancialTransaction::route('/create'),
            'edit' => Pages\EditFinancialTransaction::route('/{record}/edit'),
        ];
    }
}
