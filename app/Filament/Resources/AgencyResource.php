<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Resources\AgencyResource\RelationManagers;
use App\Models\Agency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgencyResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Référentiels & Admin';

    protected static ?string $modelLabel = 'Agence client';

    protected static ?string $pluralModelLabel = 'Agences clients';

    protected static ?int $navigationSort = 22;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité de l\'Agence')
                    ->description('Ces informations apparaîtront sur vos factures et documents officiels.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('agencies/logos')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'Agence')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('capital')
                            ->label('Capital Social')
                            ->numeric()
                            ->prefix('FCFA')
                            ->helperText('Sera affiché en bas de facture.'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email de contact'),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Téléphone de contact (Public)')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Coordonnées & Fiscalité')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse physique'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone'),
                        Forms\Components\TextInput::make('nif')
                            ->label('NIF (Numéro d\'Identification Fiscale)'),
                        Forms\Components\TextInput::make('rccm')
                            ->label('RCCM'),
                        Forms\Components\TextInput::make('website')
                            ->label('Site Web')
                            ->prefix('https://'),
                    ])->columns(2),

                Forms\Components\Section::make('Activation des Modules')
                    ->description('Choisissez les fonctionnalités actives pour cette agence.')
                    ->schema([
                        Forms\Components\CheckboxList::make('modules')
                            ->options([
                                'transit' => '🚢 Transit / Douane',
                                'commerce' => '🛒 Commerce & Distribution',
                                'hr' => '👥 Ressources Humaines',
                                'finance' => '📈 Finance & Journal de Caisse',
                                'mad' => '📦 Entreposage (MAD)',
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom Agence')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nif')
                    ->label('NIF'),
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
        return auth()->user()->hasRole('Super Admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAgencies::route('/'),
        ];
    }
}
