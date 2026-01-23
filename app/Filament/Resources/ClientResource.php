<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'CRM & Business Development';

    protected static ?string $modelLabel = 'Client / Importateur';

    protected static ?string $pluralModelLabel = 'Clients & Importateurs';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité du Client')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom / Raison Sociale')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nif')
                            ->label('NIF (Togo)')
                            ->placeholder('Ex: 1000123456')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Informations Bancaires')
                    ->description('Détails pour les virements et règlements')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nom de la Banque')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_account_number')
                            ->label('Numéro de Compte / IBAN')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_swift_bic')
                            ->label('Code SWIFT / BIC')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_address')
                            ->label('Adresse de la Banque')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bank_rib_details')
                            ->label('Détails RIB / Autres')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom / Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nif')
                    ->label('NIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dossiers_count')
                    ->label('Dossiers')
                    ->counts('dossiers')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && (in_array('transit', $agency->modules) || in_array('commerce', $agency->modules));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
