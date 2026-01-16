<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransitOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $ongoingDeclarations = \App\Models\Declaration::whereNotIn('statut', ['Sorti', 'BAE'])->count();
        $redCircuits = \App\Models\Declaration::where('circuit', 'Rouge')->count();
        
        $totalColis = \App\Models\Entree::where('statut', '!=', 'Sorti')->sum('nombre_colis');
        
        $totalProvisions = \App\Models\FinancialTransaction::where('type', 'Provision')
            ->whereMonth('date', now()->month)
            ->sum('amount');

        return [
            Stat::make('Déclarations en cours', $ongoingDeclarations)
                ->description($redCircuits . ' en circuit Rouge')
                ->descriptionIcon('heroicon-m-document-magnifying-glass')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make('Stock MAD Actuel', number_format($totalColis, 0, ',', ' ') . ' colis')
                ->description('Marchandises en entrepôt')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
            Stat::make('Provisions Encaissées (Mois)', number_format($totalProvisions, 0, ',', ' ') . ' FCFA')
                ->description('Trésorerie disponible')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
