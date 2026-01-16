<?php

namespace App\Filament\Resources\DossierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('number')
                        ->label('N° Facture')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(function() {
                            $year = date('Y');
                            $lastInvoice = \App\Models\Invoice::where('number', 'like', "FAC-{$year}-%")
                                ->orderByRaw('CAST(SUBSTR(number, -4) AS UNSIGNED) DESC')
                                ->first();
                            
                            if ($lastInvoice) {
                                $lastNum = intval(substr($lastInvoice->number, -4));
                                $nextNum = $lastNum + 1;
                            } else {
                                $nextNum = 1;
                            }
                            
                            return 'FAC-' . $year . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                        })
                        ->helperText('Modifiable si besoin.'),
                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Brouillon' => 'Brouillon',
                            'Envoyée' => 'Envoyée',
                            'Payée' => 'Payée',
                            'Annulée' => 'Annulée',
                        ])->default('Brouillon')->required(),
                ])->columns(3),

                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                        Forms\Components\TextInput::make('amount')->label('Montant')->numeric()->required()->prefix('FCFA'),
                        Forms\Components\Toggle::make('is_debours')->label('Débours (sans TVA)')->default(false),
                    ])->columns(4)->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        $items = $get('items') ?? [];
                        $subHon = 0; $subDeb = 0;
                        foreach ($items as $it) {
                            $amt = floatval($it['amount'] ?? 0);
                            if (!empty($it['is_debours'])) $subDeb += $amt; else $subHon += $amt;
                        }
                        $tax = $subHon * 0.18;
                        $set('subtotal_honoraires', $subHon);
                        $set('subtotal_debours', $subDeb);
                        $set('tax_amount', $tax);
                        $set('total_amount', $subHon + $subDeb + $tax);
                    }),
                Forms\Components\Hidden::make('subtotal_honoraires'),
                Forms\Components\Hidden::make('subtotal_debours'),
                Forms\Components\Hidden::make('tax_amount'),
                Forms\Components\Hidden::make('total_amount'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('N° Facture')
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('date')
                    ->date(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Montant TTC')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Brouillon' => 'gray',
                        'Envoyée' => 'warning',
                        'Payée' => 'success',
                        'Annulée' => 'danger',
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
