<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncotermResource\Pages;
use App\Models\Incoterm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IncotermResource extends Resource
{
    protected static ?string $model = Incoterm::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Incoterm';

    protected static ?string $pluralModelLabel = 'Incoterms (2020)';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code Incoterm (3 lettres)')
                    ->required()
                    ->maxLength(3)
                    ->placeholder('FOB'),
                Forms\Components\TextInput::make('name')
                    ->label('Libellé Complet')
                    ->required()
                    ->placeholder('Free On Board'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
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
                    ->label('Désignation')
                    ->searchable(),
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
            'index' => Pages\ManageIncoterms::route('/'),
        ];
    }
}
