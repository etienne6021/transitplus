<?php

namespace App\Filament\Resources\DossierTypeResource\Pages;

use App\Filament\Resources\DossierTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDossierTypes extends ManageRecords
{
    protected static string $resource = DossierTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
