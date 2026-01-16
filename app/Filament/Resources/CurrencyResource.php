<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Devise';

    protected static ?string $pluralModelLabel = 'Devises & Taux Change';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code Devise (3 lettres)')
                    ->required()
                    ->maxLength(3)
                    ->placeholder('XOF'),
                Forms\Components\TextInput::make('name')
                    ->label('Nom de la Devise')
                    ->required()
                    ->placeholder('Franc CFA'),
                Forms\Components\TextInput::make('symbol')
                    ->label('Symbole'),
                Forms\Components\TextInput::make('exchange_rate')
                    ->label('Taux de Change (vers XOF)')
                    ->numeric()
                    ->required()
                    ->default(1.0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Devise')
                    ->searchable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label('Symbole'),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label('Taux')
                    ->numeric(),
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
        return $agency && is_array($agency->modules) && (in_array('transit', $agency->modules) || in_array('commerce', $agency->modules));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCurrencies::route('/'),
        ];
    }
}
