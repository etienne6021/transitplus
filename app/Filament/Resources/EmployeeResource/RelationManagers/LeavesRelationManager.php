<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeavesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaves';

    protected static ?string $title = 'Congés & Absences';

    protected static ?string $modelLabel = 'Congé/Absence';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Début')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fin')
                            ->required(),
                    ]),
                Forms\Components\Select::make('type')
                    ->options([
                        'Annuel' => '🏖️ Congé Annuel',
                        'Maladie' => '🤒 Maladie',
                        'Maternité' => '👶 Maternité',
                        'Permission' => '📝 Permission Exceptionnelle',
                        'Absence' => '❌ Absence Injustifiée',
                    ])->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'En attente' => 'En attente',
                        'Approuvé' => 'Approuvé',
                        'Rejeté' => 'Rejeté',
                    ])->default('En attente'),
                Forms\Components\Textarea::make('reason')
                    ->label('Motif')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En attente' => 'warning',
                        'Approuvé' => 'success',
                        'Rejeté' => 'danger',
                        default => 'gray',
                    }),
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
