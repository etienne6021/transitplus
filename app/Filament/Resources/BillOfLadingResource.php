<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillOfLadingResource\Pages;
use App\Filament\Resources\BillOfLadingResource\RelationManagers;
use App\Models\BillOfLading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillOfLadingResource extends Resource
{
    protected static ?string $model = BillOfLading::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        $agency = auth()->user()->agency;
        return $agency && is_array($agency->modules) && in_array('transit', $agency->modules);
    }

    protected static ?string $navigationGroup = 'Transit & Douane';

    protected static ?string $modelLabel = 'Connaissement (BL)';

    protected static ?string $pluralModelLabel = 'Connaissements (BL)';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('N° de BL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('dossier_id')
                    ->relationship('dossier', 'reference')
                    ->label('Dossier de Transit')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('ship_id')
                    ->relationship('ship', 'name')
                    ->label('Navire')
                    ->searchable()
                    ->preload(),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('etd')
                            ->label('ETD (Départ prévu)'),
                        Forms\Components\DatePicker::make('eta')
                            ->label('ETA (Arrivée prévue)'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('port_loading')
                            ->label('Port de Chargement'),
                        Forms\Components\TextInput::make('port_discharge')
                            ->label('Port de Déchargement'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('dossier.reference')
                    ->label('Dossier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ship.name')
                    ->label('Navire')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eta')
                    ->label('ETA')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('port_discharge')
                    ->label('Destination'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBillOfLadings::route('/'),
        ];
    }
}
