<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomsOfficeResource\Pages;
use App\Models\CustomsOffice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomsOfficeResource extends Resource
{
    protected static ?string $model = CustomsOffice::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Bureau de Douane';
    
    protected static ?string $pluralModelLabel = 'Bureaux de Douane';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code Bureau')
                    ->placeholder('Ex: TG001')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('Nom du Bureau')
                    ->placeholder('Ex: Port de Lomé')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label('Localisation'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activé')
                    ->default(true),
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
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Ville / Lieu'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Statut')
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
            'index' => Pages\ManageCustomsOffices::route('/'),
        ];
    }
}
