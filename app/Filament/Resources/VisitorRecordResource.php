<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitorRecordResource\Pages;
use App\Models\VisitorRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisitorRecordResource extends Resource
{
    protected static ?string $model = VisitorRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Secrétariat';
    protected static ?string $modelLabel = 'Visiteur';
    protected static ?string $pluralModelLabel = 'Registre des Visiteurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Renseignement de la Visite')
                    ->icon('heroicon-o-user-plus')
                    ->description('Gestion de l\'accueil et traçabilité des visiteurs')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('visitor_name')
                                    ->label('Nom du Visiteur')
                                    ->placeholder('Prénom & Nom')
                                    ->required(),
                                Forms\Components\TextInput::make('company')
                                    ->label('Société / Entité')
                                    ->placeholder('Ex: Google, Douane...'),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->placeholder('+228...'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('visited_user_id')
                                    ->label('Collaborateur à rencontrer')
                                    ->relationship('visitedUser', 'name')
                                    ->placeholder('Sélectionner un membre')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('person_to_see')
                                    ->label('Précision (ou autre)')
                                    ->placeholder('Ex: Bureau 102'),
                                Forms\Components\TextInput::make('purpose')
                                    ->label('Motif de la visite')
                                    ->placeholder('Ex: Entretien, Livraison, Réunion...')
                                    ->required(),
                            ]),
                            
                        Forms\Components\Fieldset::make('Horaires & Observations')
                            ->schema([
                                Forms\Components\DateTimePicker::make('entry_time')
                                    ->label('Heure d\'entrée')
                                    ->required()
                                    ->seconds(false)
                                    ->default(now()),
                                Forms\Components\DateTimePicker::make('exit_time')
                                    ->label('Heure de sortie')
                                    ->seconds(false)
                                    ->helperText('À remplir au moment du départ'),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Observations particulières')
                                    ->columnSpanFull(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')->label('Visiteur')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company')->label('Société'),
                Tables\Columns\TextColumn::make('visitedUser.name')->label('Reçoit')->searchable(),
                Tables\Columns\TextColumn::make('person_to_see')->label('Précision'),
                Tables\Columns\TextColumn::make('entry_time')->label('Entrée')->dateTime('d/m H:i')->sortable(),
                Tables\Columns\TextColumn::make('exit_time')
                    ->label('Sortie')
                    ->dateTime('H:i')
                    ->placeholder('En cours...')
                    ->color(fn ($state) => $state ? 'gray' : 'success'),
                Tables\Columns\TextColumn::make('purpose')->label('Motif')->limit(20),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('sign_out')
                    ->label('Heure de sortie')
                    ->icon('heroicon-o-clock')
                    ->color('success')
                    ->action(fn (VisitorRecord $record) => $record->update(['exit_time' => now()]))
                    ->visible(fn (VisitorRecord $record) => !$record->exit_time),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVisitorRecords::route('/'),
        ];
    }
}
