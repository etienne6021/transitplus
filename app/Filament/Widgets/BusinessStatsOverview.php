<?php

namespace App\Filament\Widgets;

use App\Models\Prospect;
use App\Models\Sale;
use App\Models\Dossier;
use App\Models\FinancialTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $agencyId = auth()->user()->agency_id;

        $totalSales = Sale::where('agency_id', $agencyId)->sum('total_amount');
        $totalPaid = Sale::where('agency_id', $agencyId)->sum('paid_amount');
        $outstanding = $totalSales - $totalPaid;

        $activeDossiers = Dossier::where('agency_id', $agencyId)
            ->whereNotIn('statut', ['Clôturé', 'Livré'])
            ->count();

        $newProspects = Prospect::where('agency_id', $agencyId)
            ->where('status', 'Nouveau')
            ->count();

        return [
            Stat::make('Chiffre d\'Affaires (Ventes)', number_format($totalSales, 0, ',', ' ') . ' FCFA')
                ->description('Total des ventes enregistrées')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            Stat::make('Restes à Recouvrer', number_format($outstanding, 0, ',', ' ') . ' FCFA')
                ->description('Montant total en attente de paiement')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
            Stat::make('Dossiers Transit Actifs', $activeDossiers)
                ->description('Dossiers en cours de traitement')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
            Stat::make('Nouveaux Prospects', $newProspects)
                ->description('À qualifier rapidement')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }
}
