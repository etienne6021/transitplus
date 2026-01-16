<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectResource\Pages;
use App\Filament\Resources\ProspectResource\RelationManagers;
use App\Models\Prospect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'CRM & Business Development';

    protected static ?string $modelLabel = 'Prospect';

    protected static ?string $pluralModelLabel = 'Prospects';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Identité & Contact')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom / Entreprise')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->helperText('Ex: +22890000000'),
                                Forms\Components\Textarea::make('full_address')
                                    ->label('Adresse complète / Zone géographique'),
                            ])->columnSpan(2),
                        Forms\Components\Section::make('Qualification')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'Nouveau' => 'Nouveau',
                                        'Contacté' => 'Contacté',
                                        'Converti' => 'Converti',
                                        'Perdu' => 'Perdu',
                                    ])->default('Nouveau')
                                    ->required(),
                                Forms\Components\Select::make('priority')
                                    ->label('Priorité')
                                    ->options([
                                        'Basse' => 'Basse',
                                        'Normal' => 'Normal',
                                        'Haute' => 'Haute',
                                        'Urgent' => 'Urgent',
                                    ])->default('Normal'),
                                Forms\Components\Select::make('source')
                                    ->label('Origine du Prospect')
                                    ->options([
                                        'Port' => 'Prospection au Port de Lomé',
                                        'Recommandation' => 'Recommandation',
                                        'Terrain' => 'Visite Terrain / Zone Franche',
                                        'Appel Sortant' => 'Appel Sortant',
                                        'Web' => 'Internet / Réseaux Sociaux',
                                    ]),
                            ])->columnSpan(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom / Entreprise')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Urgent' => 'danger',
                        'Haute' => 'warning',
                        'Basse' => 'gray',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('latestInteraction.performed_at')
                    ->label('Dernier Contact')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nouveau' => 'gray',
                        'Contacté' => 'warning',
                        'Converti' => 'success',
                        'Perdu' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Origine'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('priority'),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => $record->phone ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->phone) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->phone)),
                Tables\Actions\Action::make('convert')
                    ->label('Convertir en Client')
                    ->icon('heroicon-o-user-group')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Convertir le prospect en client ?')
                    ->modalDescription('Cette action créera une fiche client officielle à partir de ces informations.')
                    ->action(function (Prospect $record) {
                        $client = \App\Models\Client::create([
                            'name' => $record->name,
                            'email' => $record->email,
                            'phone' => $record->phone,
                            'agency_id' => $record->agency_id,
                        ]);

                        // Optionnel: Mettre à jour les devis pour pointer vers le nouveau client
                        $record->quotations()->update(['client_id' => $client->id]);
                        
                        $record->update(['status' => 'Converti']);

                        \Filament\Notifications\Notification::make()
                            ->title('Prospect converti avec succès !')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Prospect $record) => $record->status !== 'Converti'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('commerce', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProspects::route('/'),
        ];
    }
}
