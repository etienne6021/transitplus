<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeclarationResource\Pages;
use App\Filament\Resources\DeclarationResource\RelationManagers;
use App\Models\Declaration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeclarationResource extends Resource
{
    protected static ?string $model = Declaration::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('transit', $agency->modules);
    }

    protected static ?string $navigationGroup = 'Transit & Douane';

    protected static ?string $modelLabel = 'Déclaration Sydonia';

    protected static ?string $pluralModelLabel = 'Déclarations Sydonia';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Déclaration en Douane')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Infos Sydonia')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\Select::make('dossier_id')
                                        ->relationship('dossier', 'reference')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Forms\Components\TextInput::make('numero_sydonia')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->label('Numéro de Déclaration OTR'),
                                ])->columns(2),
                                
                                Forms\Components\Group::make([
                                    Forms\Components\DatePicker::make('date_sydonia')
                                        ->label('Date Liquidation')
                                        ->required()
                                        ->default(now()),
                                    Forms\Components\Select::make('bureau')
                                        ->options(fn() => \App\Models\CustomsOffice::all()->pluck('name', 'code'))
                                        ->searchable()
                                        ->required(),
                                    Forms\Components\Select::make('regime')
                                        ->options(fn() => \App\Models\CustomsRegime::all()->pluck('label', 'code'))
                                        ->searchable()
                                        ->required(),
                                ])->columns(3),

                                Forms\Components\Group::make([
                                    Forms\Components\Select::make('circuit')
                                        ->options([
                                            'Vert' => 'Circuit Vert',
                                            'Jaune' => 'Circuit Jaune',
                                            'Bleu' => 'Circuit Bleu',
                                            'Rouge' => 'Circuit Rouge',
                                        ])->required(),
                                    Forms\Components\TextInput::make('valeur_douane')
                                        ->numeric()
                                        ->prefix('FCFA'),
                                    Forms\Components\TextInput::make('droits_douane')
                                        ->numeric()
                                        ->prefix('FCFA'),
                                ])->columns(3),

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('poids_total')
                                        ->label('Poids Brut Total (KG)')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('colis_total')
                                        ->label('Nombre total colis')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('manifest_num')
                                        ->label('N° Manifeste / Voyage'),
                                    Forms\Components\TextInput::make('bl_num')
                                        ->label('N° Connaissement (BL)'),
                                ])->columns(4),
                            ]),

                        Forms\Components\Tabs\Tab::make('Articles')
                            ->icon('heroicon-m-list-bullet')
                            ->schema([
                                Forms\Components\Repeater::make('articles')
                                    ->relationship()
                                    ->label('Liste des Articles')
                                    ->schema([
                                        Forms\Components\TextInput::make('description')
                                            ->label('Désignation de la marchandise')
                                            ->placeholder('Ex: Véhicule TOYOTA RAV4')
                                            ->required()
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('hscode')
                                            ->label('Code SH / Tarifaire')
                                            ->placeholder('8703...')
                                            ->required(),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('quantity')
                                                    ->label('Quantité')
                                                    ->numeric()
                                                    ->default(1),
                                                Forms\Components\TextInput::make('unit')
                                                    ->label('Unité')
                                                    ->placeholder('Colis, KG...'),
                                                Forms\Components\TextInput::make('value')
                                                    ->label('Valeur Stat.')
                                                    ->numeric()
                                                    ->prefix('FCFA'),
                                            ]),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => ($state['description'] ?? 'Article') . ' [' . ($state['hscode'] ?? '...') . ']')
                                    ->collapsible()
                                    ->cloneable()
                                    ->defaultItems(1)
                                    ->addActionLabel('Ajouter un article'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Documents Joints')
                            ->icon('heroicon-m-paper-clip')
                            ->schema([
                                Forms\Components\Repeater::make('documents')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'BL' => 'Connaissement (BL)',
                                                'Facture' => 'Facture Fournisseur',
                                                'Quittance' => 'Quittance Douane',
                                                'BAE' => 'Bon à Enlever',
                                                'Certificat' => 'Certificat d\'origine',
                                                'Autres' => 'Autres documents',
                                            ])->required(),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Nom du fichier / Titre')
                                            ->required(),
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('Uploader le document')
                                            ->directory('declarations/docs')
                                            ->required(),
                                    ])->columns(3)
                                    ->collapsible(),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_sydonia')
                    ->label('N° Sydonia')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('dossier.reference')
                    ->label('Dossier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('circuit')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Vert' => 'success',
                        'Jaune' => 'warning',
                        'Bleu' => 'primary',
                        'Rouge' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Articles')
                    ->badge(),
                Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Docs')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'BAE' => 'success',
                        'Sorti' => 'primary',
                        default => 'warning',
                    }),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeclarations::route('/'),
            'create' => Pages\CreateDeclaration::route('/create'),
            'edit' => Pages\EditDeclaration::route('/{record}/edit'),
        ];
    }
}
