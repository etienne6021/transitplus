<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Ressources Humaines';

    protected static ?string $modelLabel = 'Personnel';

    protected static ?string $pluralModelLabel = 'Personnel';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Dossier de l\'Employé')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Profil Personnel')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('first_name')
                                            ->label('Prénom')
                                            ->required(),
                                        Forms\Components\TextInput::make('last_name')
                                            ->label('Nom')
                                            ->required(),
                                        Forms\Components\TextInput::make('email')
                                            ->email(),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contrat & Salaire')
                            ->icon('heroicon-m-briefcase')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('position')
                                            ->label('Poste / Fonction')
                                            ->required(),
                                        Forms\Components\Select::make('contract_type')
                                            ->label('Type de contrat')
                                            ->options([
                                                'CDI' => 'CDI (Indéterminé)',
                                                'CDD' => 'CDD (Déterminé)',
                                                'Stage' => 'Stage / Apprentissage',
                                                'Prestataire' => 'Prestataire externe',
                                            ])->default('CDI'),
                                        Forms\Components\DatePicker::make('hire_date')
                                            ->label('Date d\'embauche')
                                            ->default(now()),
                                        Forms\Components\TextInput::make('salary')
                                            ->label('Salaire de base (Brut)')
                                            ->numeric()
                                            ->prefix('FCFA')
                                            ->required(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('CNSS & Banque')
                            ->icon('heroicon-m-building-library')
                            ->schema([
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('cnss_number')
                                            ->label('N° Affiliation CNSS')
                                            ->placeholder('Ex: 123456789'),
                                        Forms\Components\TextInput::make('account_number')
                                            ->label('Coordonnées Bancaires / RIB')
                                            ->placeholder('Banque - Code - N° Compte'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Notes & Suivi')
                            ->icon('heroicon-m-chat-bubble-bottom-center-text')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('Observations RH / Notes de suivi')
                                    ->placeholder('Points forts, incidents, objectifs...')
                                    ->rows(8),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name') // Via accessor or raw
                    ->label('Nom Complet')
                    ->state(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('position')
                    ->label('Poste')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('salary')
                    ->label('Salaire')
                    ->money('XOF'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\LeavesRelationManager::class,
            RelationManagers\PayrollsRelationManager::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('hr', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEmployees::route('/'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
