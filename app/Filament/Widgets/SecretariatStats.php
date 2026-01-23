<?php

namespace App\Filament\Widgets;

use App\Models\VisitorRecord;
use App\Models\MailRecord;
use App\Models\InternalNote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;

class SecretariatStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        try {
            $visitorsToday = VisitorRecord::whereDate('entry_time', now())->count();
            $visitorsCurrent = VisitorRecord::whereNull('exit_time')->count();
            
            $urgentMail = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('mail_records')) {
                $urgentMail = MailRecord::where('statut', 'Urgent')->count();
            }

            $activeNotes = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('internal_notes')) {
                $activeNotes = InternalNote::where('is_active', true)->count();
            }

            return [
                Stat::make('Visiteurs du jour', $visitorsToday)
                    ->description($visitorsCurrent . ' actuellement présents')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('info'),

                Stat::make('Courriers Urgents', $urgentMail)
                    ->description('Nécéssitant une attention immédiate')
                    ->descriptionIcon('heroicon-m-envelope-open')
                    ->color($urgentMail > 0 ? 'danger' : 'success'),

                Stat::make('Notes de Service', $activeNotes)
                    ->description('Notes actives en cours')
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('primary'),
            ];
        } catch (\Exception $e) {
            Log::error("SecretariatStats Widget Error: " . $e->getMessage());
            return [];
        }
    }
}
