<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternalNoteResource\Pages;
use App\Models\InternalNote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InternalNoteResource extends Resource
{
    protected static ?string $model = InternalNote::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Secrétariat';
    protected static ?string $modelLabel = 'Note de Service';
    protected static ?string $pluralModelLabel = 'Notes de Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rédaction de la Note')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Référence Numéro')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ex: ND/2026/001'),
                        Forms\Components\DatePicker::make('date_published')
                            ->label('Date de Publication')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('title')
                            ->label('Objet / Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu de la Note')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Version Scannée (PDF)')
                            ->directory('notes-de-service')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Note active')
                            ->default(true),
                        Forms\Components\Hidden::make('author_id')
                            ->default(auth()->id()),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_published')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Objet')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Signataire'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Statut')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageInternalNotes::route('/'),
        ];
    }
}
