<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = \Spatie\Activitylog\Models\Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Paramètres Système';

    protected static ?string $modelLabel = 'Journal d\'Audit';

    protected static ?string $pluralModelLabel = 'Journaux d\'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'action')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('causer_name')
                                    ->label('Auteur')
                                    ->content(fn ($record) => $record->causer?->name ?? 'Système / Automatique'),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Date & Heure')
                                    ->content(fn ($record) => $record->created_at->format('d/m/Y H:i:s')),
                                Forms\Components\Placeholder::make('description')
                                    ->label('Type d\'action')
                                    ->content(fn ($record) => match($record->description) {
                                        'created' => '🆕 Création',
                                        'updated' => '📝 Modification',
                                        'deleted' => '🗑️ Suppression',
                                        default => $record->description,
                                    }),
                                Forms\Components\Placeholder::make('subject')
                                    ->label('Élément concerné')
                                    ->content(fn ($record) => (str_replace('App\\Models\\', '', $record->subject_type)) . ' (ID: ' . $record->subject_id . ')'),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Modifications')
                    ->description('Comparaison des valeurs avant et après l\'action.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\KeyValue::make('properties.old')
                                    ->label('Anciennes Valeurs')
                                    ->keyLabel('Champ')
                                    ->valueLabel('Valeur')
                                    ->columnSpan(1),
                                Forms\Components\KeyValue::make('properties.attributes')
                                    ->label('Nouvelles Valeurs')
                                    ->keyLabel('Champ')
                                    ->valueLabel('Valeur')
                                    ->columnSpan(1),
                            ]),
                    ])->visible(fn ($record) => !empty($record->properties->all())),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date/Heure')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Utilisateur')
                    ->default('Système')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Module')
                    ->formatStateUsing(fn ($state) => str_replace('App\\Models\\', '', $state)),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Filtrer par module')
                    ->options([
                        'App\\Models\\Dossier' => 'Dossier Transit',
                        'App\\Models\\Invoice' => 'Facture Transit',
                        'App\\Models\\Sale' => 'Vente / Commerce',
                        'App\\Models\\Quotation' => 'Devis / Proforma',
                        'App\\Models\\Employee' => 'Employé',
                        'App\\Models\\Entree' => 'MAD - Entrée',
                        'App\\Models\\Sortie' => 'MAD - Sortie',
                        'App\\Models\\User' => 'Utilisateur',
                        'App\\Models\\Leave' => 'Congé / Absence',
                        'App\\Models\\Payroll' => 'Bulletin de Paie',
                        'App\\Models\\Product' => 'Produit / Stock',
                        'App\\Models\\FinancialTransaction' => 'Caisse / Transaction',
                        'App\\Models\\Client' => 'Client / Importateur',
                        'App\\Models\\Prospect' => 'Prospect CRM',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageActivities::route('/'),
        ];
    }
}
