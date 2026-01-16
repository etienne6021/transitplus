<?php

namespace App\Filament\Resources\EntreeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SortiesRelationManager extends RelationManager
{
    protected static string $relationship = 'sorties';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference_sortie')
                            ->label('Réf. Bon de Sortie')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_sortie')
                            ->required()
                            ->default(now()),
                    ]),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('inspecteur_douane')
                            ->label('Inspecteur Douane'),
                        Forms\Components\TextInput::make('scelle_numero')
                            ->label('N° Scellé'),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre_colis_sortis')
                            ->label('Nombre de colis sortis')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->suffix(fn ($livewire) => " / " . ($livewire->getOwnerRecord()->nombre_colis - $livewire->getOwnerRecord()->sorties()->where('id', '!=', null)->sum('nombre_colis_sortis')) . " disponibles")
                            ->rules([
                                fn ($livewire): \Closure => function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                    $entree = $livewire->getOwnerRecord();
                                    $alreadyOut = $entree->sorties()
                                        ->when($livewire->getMountedTableActionRecord(), fn($q, $record) => $q->where('id', '!=', $record))
                                        ->sum('nombre_colis_sortis');
                                    
                                    $available = $entree->nombre_colis - $alreadyOut;
                                    
                                    if ($value > $available) {
                                        $fail("L'entrepot MAD ne contient que {$available} colis. Impossible d'en sortir {$value}.");
                                    }
                                },
                            ]),
                        Forms\Components\TextInput::make('receptionnaire')
                            ->label('Réceptionnaire / Chauffeur'),
                    ]),
                
                Forms\Components\FileUpload::make('bordereau_sortie')
                    ->label('Bon de Sortie signé (Scan)')
                    ->directory('mad/sorties')
                    ->openable()
                    ->previewable(true)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference_sortie')
            ->columns([
                Tables\Columns\TextColumn::make('reference_sortie')
                    ->label('N° Sortie')
                    ->weight('bold')
                    ->description(fn($record) => "Scellé: " . ($record->scelle_numero ?? 'N/A')),
                Tables\Columns\TextColumn::make('date_sortie')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_colis_sortis')
                    ->label('Qté')
                    ->badge()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('inspecteur_douane')
                    ->label('Inspecteur'),
                Tables\Columns\IconColumn::make('bordereau_sortie')
                    ->label('Doc')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->url(fn ($record) => $record->bordereau_sortie ? asset('storage/' . $record->bordereau_sortie) : null)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
