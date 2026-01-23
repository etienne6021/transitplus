<?php

namespace App\Filament\Resources\AgendaItemResource\Pages;

use App\Filament\Resources\AgendaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAgendaItems extends ManageRecords
{
    protected static string $resource = AgendaItemResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\CalendarWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // On enlève le bouton ici car le calendrier permet déjà la création
        ];
    }
}
