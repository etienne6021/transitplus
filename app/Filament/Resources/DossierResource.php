<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DossierResource\Pages;
use App\Filament\Resources\DossierResource\RelationManagers;
use App\Models\Dossier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DossierResource extends Resource
{
    protected static ?string $model = Dossier::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationGroup = 'Transit & Douane';

    protected static ?string $modelLabel = 'Dossier de Transit';

    protected static ?string $pluralModelLabel = 'Dossiers de Transit';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // Section Opérationnelle (Gauche)
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Identité du Dossier')
                                ->icon('heroicon-m-folder')
                                ->schema([
                                    Forms\Components\Group::make([
                                        Forms\Components\TextInput::make('reference')
                                            ->label('Référence Dossier')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->default('TR-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(2))))
                                            ->disabledOn('edit')
                                            ->extraInputAttributes(['class' => 'text-primary-600 font-bold']),
                                        Forms\Components\Select::make('client_id')
                                            ->relationship('client', 'name')
                                            ->label('Client / Importateur')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ])->columns(2),
                                    
                                    Forms\Components\Group::make([
                                        Forms\Components\Select::make('type')
                                            ->options(fn() => \App\Models\DossierType::all()->pluck('name', 'name'))
                                            ->required(),
                                        Forms\Components\Select::make('mode')
                                            ->options([
                                                'Maritime' => 'Maritime (Lomé Port)',
                                                'Aérien' => 'Aérien (Aéroport Gnassingbé Eyadéma)',
                                                'Terrestre' => 'Terrestre (Frontières)',
                                            ])->required(),
                                        Forms\Components\Select::make('statut')
                                            ->options([
                                                'Ouvert' => 'Dossier Ouvert',
                                                'En cours' => 'En cours de dédouanement',
                                                'Clôturé' => 'Dossier Clôturé',
                                            ])->required()
                                            ->default('Ouvert'),
                                    ])->columns(3),
                                    
                                    Forms\Components\Textarea::make('description')
                                        ->label('Commentaires / Instructions particulières')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(2),

                        // Section Financière QuickView (Droite)
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('Résumé financier')
                                ->icon('heroicon-m-banknotes')
                                ->description('Aperçu Provisions vs Débours')
                                ->schema([
                                    Forms\Components\Placeholder::make('provision_total')
                                        ->label('Total Provisions')
                                        ->content(fn ($record) => $record ? number_format($record->transactions()->where('type', 'Provision')->sum('amount'), 0, ',', ' ') . ' FCFA' : '0 FCFA'),
                                    Forms\Components\Placeholder::make('debours_total')
                                        ->label('Total Débours')
                                        ->content(fn ($record) => $record ? number_format($record->transactions()->where('type', 'Débours')->sum('amount'), 0, ',', ' ') . ' FCFA' : '0 FCFA'),
                                    
                                    Forms\Components\Placeholder::make('balance_display')
                                        ->label('Solde Actuel')
                                        ->content(function ($record) {
                                            if (!$record) return '0 FCFA';
                                            $bal = $record->transactions()->where('type', 'Provision')->sum('amount') - $record->transactions()->where('type', 'Débours')->sum('amount');
                                            $color = $bal < 0 ? 'text-danger-600' : 'text-success-600';
                                            return new \Illuminate\Support\HtmlString("<span class='text-2xl font-bold {$color}'>" . number_format($bal, 0, ',', ' ') . " FCFA</span>");
                                        }),
                                ])->collapsible(),

                            Forms\Components\Section::make('Pièces Jointes')
                                ->icon('heroicon-m-paper-clip')
                                ->schema([
                                    Forms\Components\Repeater::make('documents')
                                        ->relationship()
                                        ->schema([
                                            Forms\Components\TextInput::make('title')->label('Nom')->required(),
                                            Forms\Components\FileUpload::make('file_path')->label('Fichier')->directory('dossiers/docs'),
                                        ])->collapsible()->collapsed()
                                ])->collapsible(),
                        ])->columnSpan(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable()
                    ->label('Client / Importateur'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Import' => 'primary',
                        'Export' => 'success',
                        'Transit' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('mode')
                    ->icon(fn (string $state): string => match ($state) {
                        'Maritime' => 'heroicon-m-stop',
                        'Aérien' => 'heroicon-m-paper-airplane',
                        'Terrestre' => 'heroicon-m-truck',
                        default => 'heroicon-m-question-mark-circle',
                    }),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Ouvert' => 'gray',
                        'En cours' => 'primary',
                        'Clôturé' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Solde')
                    ->money('XOF')
                    ->state(function (Dossier $record) {
                        $provisions = $record->transactions()->where('type', 'Provision')->sum('amount');
                        $debours = $record->transactions()->where('type', 'Débours')->sum('amount');
                        return $provisions - $debours;
                    })
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Docs')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date d\'ouverture')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'Import' => 'Importation',
                        'Export' => 'Exportation',
                        'Transit' => 'Transit',
                    ]),
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'Ouvert' => 'Ouvert',
                        'En cours' => 'En cours',
                        'Clôturé' => 'Clôturé',
                    ]),
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
            RelationManagers\BillOfLadingsRelationManager::class,
            RelationManagers\DeclarationsRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\EntreesRelationManager::class,
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
            'index' => Pages\ListDossiers::route('/'),
            'create' => Pages\CreateDossier::route('/create'),
            'edit' => Pages\EditDossier::route('/{record}/edit'),
        ];
    }
}
