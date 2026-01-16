<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Paramètres Système';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité de l\'Utilisateur')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email professionnel')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('agency_id')
                            ->relationship('agency', 'name')
                            ->label('Agence de rattachement')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()->hasRole('Super Admin'))
                            ->default(fn () => auth()->user()->agency_id),
                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Droits & Permissions')
                    ->description('Assignez un rôle pour définir les accès de cet utilisateur.')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Rôles assignés'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agence')
                    ->badge()
                    ->color('gray')
                    ->visible(fn () => auth()->user()->hasRole('Super Admin')),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('agency_id')
                    ->relationship('agency', 'name')
                    ->label('Filtrer par agence')
                    ->visible(fn () => auth()->user()->hasRole('Super Admin')),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (!auth()->user()->hasRole('Super Admin')) {
            $query->where('agency_id', auth()->user()->agency_id);
        }

        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
