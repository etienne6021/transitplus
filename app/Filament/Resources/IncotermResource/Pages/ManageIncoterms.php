<?php

namespace App\Filament\Resources\IncotermResource\Pages;

use App\Filament\Resources\IncotermResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIncoterms extends ManageRecords
{
    protected static string $resource = IncotermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
