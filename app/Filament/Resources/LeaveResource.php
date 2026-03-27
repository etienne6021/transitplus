<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Capital Humain';

    protected static ?string $modelLabel = 'Congé';

    protected static ?string $pluralModelLabel = 'Congés';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->label('Employé')
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->state(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->searchable(['employees.first_name', 'employees.last_name']),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
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
        return $agency && is_array($agency->modules) && in_array('hr', $agency->modules);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeaves::route('/'),
        ];
    }
}
