<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailRecordResource\Pages;
use App\Models\MailRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MailRecordResource extends Resource
{
    protected static ?string $model = MailRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Secrétariat';
    protected static ?string $modelLabel = 'Courrier';
    protected static ?string $pluralModelLabel = 'Gestion du Courrier';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Enregistrement du Courrier')
                    ->icon('heroicon-o-envelope')
                    ->description('Détails officiels du courrier entrant ou sortant')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('reference')
                                    ->label('Référence')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefix('REF-'),
                                Forms\Components\Select::make('type')
                                    ->label('Type de flux')
                                    ->options(['Arrivée' => 'Arrivée (Inbox)', 'Départ' => 'Départ (Outbox)'])
                                    ->required()
                                    ->native(false),
                                Forms\Components\DatePicker::make('date_record')
                                    ->label('Date d\'enregistrement')
                                    ->required()
                                    ->default(now()),
                            ]),
                        
                        Forms\Components\TextInput::make('sender_receiver')
                            ->label('Expéditeur / Destinataire')
                            ->placeholder('Nom de la personne ou entreprise')
                            ->required()
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('subject')
                            ->label('Objet / Titre du courrier')
                            ->required()
                            ->columnSpanFull(),
                            
                        Forms\Components\RichEditor::make('description')
                            ->label('Résumé du contenu')
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('scanned_file')
                            ->label('Numérisation (PDF / Scan)')
                            ->directory('mail-scans')
                            ->image()
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('statut')
                            ->label('Niveau de traitement')
                            ->options([
                                'En attente' => 'En attente',
                                'Traité' => 'Traité',
                                'Urgent' => 'Urgent / Prioritaire',
                            ])
                            ->required()
                            ->default('En attente')
                            ->native(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => preg_match('/Arrivée/', $state) ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('date_record')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('sender_receiver')->label('Correspondant')->searchable(),
                Tables\Columns\TextColumn::make('subject')->label('Objet')->limit(30),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Urgent' => 'danger',
                        'Traité' => 'success',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(['Arrivée' => 'Arrivée', 'Départ' => 'Départ']),
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
            'index' => Pages\ManageMailRecords::route('/'),
        ];
    }
}
