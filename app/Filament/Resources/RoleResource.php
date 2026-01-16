<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = \Spatie\Permission\Models\Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Paramètres Système';

    protected static ?string $modelLabel = 'Rôle & Droits';

    protected static ?string $pluralModelLabel = 'Rôles & Droits';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du rôle')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('permissions')
                    ->label('Permissions associées')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rôle')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Nombre de permissions')
                    ->counts('permissions'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
