<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Pays';

    protected static ?string $pluralModelLabel = 'Liste des Pays';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code ISO (2 lettres)')
                    ->required()
                    ->maxLength(2)
                    ->placeholder('TG'),
                Forms\Components\TextInput::make('name')
                    ->label('Nom du Pays')
                    ->required()
                    ->placeholder('Togo'),
                Forms\Components\TextInput::make('phone_code')
                    ->label('Code Tél.')
                    ->placeholder('+228'),
                Forms\Components\TextInput::make('emoji')
                    ->label('Drapeau (Emoji)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('emoji')
                    ->label(''),
                Tables\Columns\TextColumn::make('code')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_code')
                    ->label('Indicatif'),
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
            'index' => Pages\ManageCountries::route('/'),
        ];
    }
}
