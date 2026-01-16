<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationGroup = 'DocuSync & Rapports';

    protected static ?string $modelLabel = 'Archive / Courrier';

    protected static ?string $pluralModelLabel = 'Archives & Courriers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Indexation Document')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre / Référence')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Type de document')
                            ->options([
                                'Courrier Arrivée' => 'Courrier Arrivée',
                                'Courrier Départ' => 'Courrier Départ',
                                'Archive' => 'Archive (Scan)',
                                'Facture Fournisseur' => 'Facture Fournisseur',
                                'Divers' => 'Divers',
                            ])->required(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document numérisé')
                            ->directory('documents')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date d\'indexation')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ManageDocuments::route('/'),
        ];
    }
}
