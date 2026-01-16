<?php

namespace App\Filament\Resources\ProspectResource\Pages;

use App\Filament\Resources\ProspectResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProspects extends ManageRecords
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
