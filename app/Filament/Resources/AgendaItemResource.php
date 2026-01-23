<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaItemResource\Pages;
use App\Models\AgendaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgendaItemResource extends Resource
{
    protected static ?string $model = AgendaItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Collaboration';

    protected static ?string $modelLabel = 'Agenda & Tâches';

    protected static ?string $pluralModelLabel = 'Agenda Personnel';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'événement')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description / Notes')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Début')
                            ->required()
                            ->seconds(false)
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('Fin')
                            ->seconds(false),
                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'Réunion' => 'Réunion',
                                'Appel' => 'Appel',
                                'Visite' => 'Visite Client',
                                'Tâche' => 'Tâche Administrative',
                                'Autre' => 'Autre',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'En attente' => 'En attente',
                                'Terminé' => 'Terminé',
                                'Annulé' => 'Annulé',
                            ])
                            ->required()
                            ->default('En attente'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Couleur d\'affichage')
                            ->default('#3b82f6'),
                        Forms\Components\Toggle::make('is_public')
                            ->label('Partager avec l\'agence')
                            ->helperText('Si activé, les autres membres de l\'agence pourront voir cet événement.')
                            ->default(false),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Propriétaire de l\'agenda')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->visible(fn () => auth()->user()->can('gestion_agenda_autres'))
                            ->required()
                            ->searchable(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('')
                    ->width('40px'),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (AgendaItem $record): string => $record->end_time ? 'Jusqu\'à ' . $record->end_time->format('H:i') : ''),
                Tables\Columns\TextColumn::make('title')
                    ->label('Événement')
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Réunion' => 'primary',
                        'Appel' => 'info',
                        'Visite' => 'success',
                        'Tâche' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Terminé' => 'success',
                        'Annulé' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->trueIcon('heroicon-o-users')
                    ->falseIcon('heroicon-o-lock-closed'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pour')
                    ->sortable(),
            ])
            ->defaultSort('start_time', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Agenda de')
                    ->relationship('user', 'name')
                    ->visible(fn () => auth()->user()->can('gestion_agenda_autres')),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Filtrer par catégorie')
                    ->options([
                        'Réunion' => 'Réunion',
                        'Appel' => 'Appel',
                        'Visite' => 'Visite Client',
                        'Tâche' => 'Tâche Administrative',
                    ]),
                Tables\Filters\TernaryFilter::make('only_mine')
                    ->label('Mes événements uniquement')
                    ->placeholder('Tous les événements visibles')
                    ->trueLabel('Mes événements')
                    ->falseLabel('Partagés avec moi')
                    ->queries(
                        true: fn (Builder $query) => $query->where('user_id', auth()->id()),
                        false: fn (Builder $query) => $query->where('user_id', '!=', auth()->id()),
                        blank: fn (Builder $query) => $query,
                    )
                    ->default(fn () => !auth()->user()->can('gestion_agenda_autres')),
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

    public static function getEloquentQuery(): Builder
    {
        // Si l'utilisateur a le droit de gestion, il voit tout son agence
        if (auth()->user()->can('gestion_agenda_autres')) {
            return parent::getEloquentQuery();
        }

        // Sinon, on ne montre que ses propres événements OU les événements publics de l'agence
        return parent::getEloquentQuery()
            ->where(function (Builder $query) {
                $query->where('user_id', auth()->id())
                      ->orWhere('is_public', true);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAgendaItems::route('/'),
        ];
    }
}
