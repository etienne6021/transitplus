<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DossierTypeResource\Pages;
use App\Models\DossierType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DossierTypeResource extends Resource
{
    protected static ?string $model = DossierType::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Type de Dossier';

    protected static ?string $pluralModelLabel = 'Types de Dossiers';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du Type')
                    ->placeholder('Ex: Importation, Exportation')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Code (optionnel)'),
                Forms\Components\Select::make('color')
                    ->label('Couleur Badge')
                    ->options([
                        'primary' => 'Bleu (Principal)',
                        'info' => 'Bleu Ciel (Info)',
                        'success' => 'Vert (Succès)',
                        'warning' => 'Jaune (Alerte)',
                        'danger' => 'Rouge (Danger)',
                        'gray' => 'Gris',
                    ]),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('color')
                    ->badge()
                    ->color(fn ($state) => $state ?? 'gray'),
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
            'index' => Pages\ManageDossierTypes::route('/'),
        ];
    }
}
