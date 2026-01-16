<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipResource\Pages;
use App\Filament\Resources\ShipResource\RelationManagers;
use App\Models\Ship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipResource extends Resource
{
    protected static ?string $model = Ship::class;

    protected static ?string $navigationIcon = 'heroicon-o-stop';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Navire';

    protected static ?string $pluralModelLabel = 'Navires';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du Navire')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('flag')
                    ->label('Pavillon / Origine')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom du Navire')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('flag')
                    ->label('Pavillon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enregistré le')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('transit', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShips::route('/'),
        ];
    }
}
