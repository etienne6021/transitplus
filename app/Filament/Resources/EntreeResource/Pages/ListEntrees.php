<?php

namespace App\Filament\Resources\EntreeResource\Pages;

use App\Filament\Resources\EntreeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntrees extends ListRecords
{
    protected static string $resource = EntreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvelle Entrée'),
        ];
    }
}
