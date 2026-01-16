<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingLineResource\Pages;
use App\Models\ShippingLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingLineResource extends Resource
{
    protected static ?string $model = ShippingLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Compagnie Maritime / Armateur';

    protected static ?string $pluralModelLabel = 'Compagnies & Armateurs';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom de la Compagnie')
                    ->required()
                    ->placeholder('Ex: MSC, MAERSK, COSCO'),
                Forms\Components\TextInput::make('code')
                    ->label('Code SCAC / Identifiant'),
                Forms\Components\TextInput::make('contact_person')
                    ->label('Interlocuteur'),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\TextInput::make('phone')
                    ->tel(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ManageShippingLines::route('/'),
        ];
    }
}
