<?php

namespace App\Filament\Widgets;

use App\Models\Prospect;
use App\Models\Sale;
use App\Models\Dossier;
use App\Models\Declaration;
use App\Models\Entree;
use App\Models\FinancialTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        try {
            // CA & Recouvrement
            $totalSales = Sale::sum('total_amount') ?? 0;
            $totalPaid = Sale::sum('paid_amount') ?? 0;
            $outstanding = $totalSales - $totalPaid;

            // Transit & MAD
            $activeDossiers = Dossier::whereNotIn('statut', ['Clôturé', 'Livré'])->count();
            $ongoingDeclarations = Declaration::whereNotIn('statut', ['Sorti', 'BAE'])->count();
            
            // Stock MAD (Volume total en attente de sortie)
            // On vérifie si la table existe et a la colonne avant de sommer pour éviter le crash
            $totalColisMAD = 0;
            if (\Illuminate\Support\Facades\Schema::hasColumn('entrees', 'statut')) {
                 $totalColisMAD = Entree::where('statut', '!=', 'Sorti')->sum('nombre_colis');
            }

            // CRM
            $newProspects = Prospect::where('status', 'Nouveau')->count();

            // Trésorerie
            $monthlyProvision = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('financial_transactions')) {
                $monthlyProvision = FinancialTransaction::where('type', 'Provision')
                    ->whereMonth('date', now()->month)
                    ->sum('amount');
            }

            return [
                Stat::make('Chiffre d\'Affaires', number_format($totalSales, 0, ',', ' ') . ' FCFA')
                    ->description('Total des ventes validées')
                    ->descriptionIcon('heroicon-m-shopping-cart')
                    ->color('success'),
                    
                Stat::make('Restes à Recouvrer', number_format($outstanding, 0, ',', ' ') . ' FCFA')
                    ->description('Encours clients à régulariser')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('danger'),

                Stat::make('Dossiers Transit', $activeDossiers)
                    ->description($ongoingDeclarations . ' déclarations en cours')
                    ->descriptionIcon('heroicon-m-truck')
                    ->color('info'),

                Stat::make('Gestion MAD', number_format($totalColisMAD, 0, ',', ' ') . ' colis')
                    ->description('Volume actuel en entrepôt')
                    ->descriptionIcon('heroicon-m-cube')
                    ->color('warning'),

                Stat::make('Force de Vente', $newProspects)
                    ->description('Nouveaux prospects à qualifier')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->color('primary'),

                Stat::make('Trésorerie (Mois)', number_format($monthlyProvision, 0, ',', ' ') . ' FCFA')
                    ->description('Provisions transit encaissées')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('success'),
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur Dashboard Widget: " . $e->getMessage());
            return [
                Stat::make('Dashboard', 'Indisponible')
                    ->description('Une erreur est survenue lors du calcul des stats')
                    ->color('danger')
            ];
        }
    }
}
