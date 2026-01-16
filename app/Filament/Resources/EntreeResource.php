<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntreeResource\Pages;
use App\Filament\Resources\EntreeResource\RelationManagers;
use App\Models\Entree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntreeResource extends Resource
{
    protected static ?string $model = Entree::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationGroup = 'Logistique MAD';

    protected static ?string $modelLabel = 'Entrée MAD';

    protected static ?string $pluralModelLabel = 'Entrées MAD';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Suivi du dossier MAD')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Indexation & Arrivée')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('dossier_id')
                                            ->relationship('dossier', 'reference')
                                            ->label('Dossier Transit')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('reference_mad')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->label('N° Référence MAD')
                                            ->placeholder('Ex: MAD-2024-001')
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\TextInput::make('nature_marchandise')
                                    ->label('Nature de la marchandise')
                                    ->placeholder('Ex: Véhicules, Électronique, Divers...')
                                    ->required(),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_arrivee')
                                            ->label('Date d\'arrivée')
                                            ->required()
                                            ->default(now()),
                                        Forms\Components\TextInput::make('provenance')
                                            ->required()
                                            ->label('Origine')
                                            ->placeholder('Ex: Port de Lomé'),
                                        Forms\Components\TextInput::make('conducteur')
                                            ->label('Transporteur / Chauffeur'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contrôle & Conformité')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('control_officer')
                                            ->label('Officier de contrôle'),
                                        Forms\Components\DatePicker::make('control_date')
                                            ->label('Date du contrôle'),
                                    ]),
                                Forms\Components\Select::make('statut')
                                    ->label('État administratif')
                                    ->options([
                                        'En attente' => '🕙 En attente de déchargement',
                                        'Reçu' => '✅ En magasin (Stocké)',
                                        'Sorti' => '🚢 Sorti du MAD',
                                    ])->default('En attente'),
                                Forms\Components\FileUpload::make('bordereau_entree')
                                    ->label('Bordereau d\'Entrée signé (Scan/PDF)')
                                    ->directory('mad/bordereaux')
                                    ->openable()
                                    ->previewable(true),
                            ]),

                        Forms\Components\Tabs\Tab::make('Inventaire & Avaries')
                            ->icon('heroicon-m-archive-box-x-mark')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre_colis')
                                            ->label('Quantité Totale Entrante')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->suffix('Colis'),
                                        Forms\Components\TextInput::make('nb_colis_avaries')
                                            ->label('Nombre de colis avariés')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('Colis'),
                                    ]),
                                Forms\Components\Textarea::make('description_avaries')
                                    ->label('Nature des avaries / Observations')
                                    ->rows(3)
                                    ->placeholder('Détaillez ici l\'état des colis abîmés...'),
                                
                                Forms\Components\Section::make('Solde MAGASIN (Temps réel)')
                                    ->schema([
                                        Forms\Components\Placeholder::make('stock_info')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) return 'Disponible après enregistrement.';
                                                $sortis = $record->sorties()->sum('nombre_colis_sortis');
                                                $restant = $record->nombre_colis - $sortis;
                                                return new \Illuminate\Support\HtmlString("
                                                    <div class='flex justify-between items-center p-4 bg-gray-50 rounded-lg'>
                                                        <div class='text-center'>
                                                            <p class='text-xs text-gray-500 uppercase'>Total Reçu</p>
                                                            <p class='text-xl font-bold'>{$record->nombre_colis}</p>
                                                        </div>
                                                        <div class='text-center'>
                                                            <p class='text-xs text-gray-500 uppercase'>Avariés</p>
                                                            <p class='text-xl font-bold text-danger-600'>{$record->nb_colis_avaries}</p>
                                                        </div>
                                                        <div class='text-center'>
                                                            <p class='text-xs text-gray-500 uppercase'>Déjà Libérés</p>
                                                            <p class='text-xl font-bold text-success-600'>{$sortis}</p>
                                                        </div>
                                                        <div class='text-center px-4 py-2 bg-primary-600 rounded text-white'>
                                                            <p class='text-xs uppercase'>Reste en MAD</p>
                                                            <p class='text-2xl font-bold'>{$restant}</p>
                                                        </div>
                                                    </div>
                                                ");
                                            }),
                                    ])->visible(fn ($record) => $record !== null),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_mad')
                    ->label('Réf. MAD')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable(),
                Tables\Columns\TextColumn::make('dossier.reference')
                    ->label('Dossier')
                    ->searchable()
                    ->description(fn ($record) => $record->dossier?->client?->name),
                Tables\Columns\TextColumn::make('date_arrivee')
                    ->label('Arrivée')
                    ->date()
                    ->sortable()
                    ->since()
                    ->description(fn ($state) => $state),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'En attente' => 'warning',
                        'Reçu' => 'success',
                        'Sorti' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nombre_colis')
                    ->label('Total')
                    ->numeric()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('colis_restants')
                    ->label('En Stock')
                    ->state(fn ($record) => $record->nombre_colis - $record->sorties()->sum('nombre_colis_sortis'))
                    ->weight('bold')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->alignment('center'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut'),
                Tables\Filters\TernaryFilter::make('en_stock')
                    ->label('Filtrer par stock')
                    ->placeholder('Tous les dossiers')
                    ->trueLabel('Uniquement en stock')
                    ->falseLabel('Sorties complètes')
                    ->queries(
                        true: fn (Builder $query) => $query->whereRaw('(nombre_colis - (select coalesce(sum(nombre_colis_sortis), 0) from sorties where entree_id = entrees.id)) > 0'),
                        false: fn (Builder $query) => $query->whereRaw('(nombre_colis - (select coalesce(sum(nombre_colis_sortis), 0) from sorties where entree_id = entrees.id)) <= 0'),
                    )
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
            RelationManagers\SortiesRelationManager::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('mad', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntrees::route('/'),
            'create' => Pages\CreateEntree::route('/create'),
            'edit' => Pages\EditEntree::route('/{record}/edit'),
        ];
    }
}
