<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomsRegimeResource\Pages;
use App\Models\CustomsRegime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomsRegimeResource extends Resource
{
    protected static ?string $model = CustomsRegime::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Configuration & Référentiels';

    protected static ?string $modelLabel = 'Régime Douanier';

    protected static ?string $pluralModelLabel = 'Régimes Douaniers';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code Régime')
                    ->placeholder('Ex: IM4, EX1, C100')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('label')
                    ->label('Libellé')
                    ->placeholder('Ex: Mise à la consommation (Directe)')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'Import' => 'Importation',
                        'Export' => 'Exportation',
                        'Transit' => 'Transit / Re-Export',
                    ])->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Import' => 'info',
                        'Export' => 'warning',
                        'Transit' => 'success',
                        default => 'gray',
                    }),
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
            'index' => Pages\ManageCustomsRegimes::route('/'),
        ];
    }
}
