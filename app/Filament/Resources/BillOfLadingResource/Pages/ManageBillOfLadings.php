<?php

namespace App\Filament\Resources\BillOfLadingResource\Pages;

use App\Filament\Resources\BillOfLadingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBillOfLadings extends ManageRecords
{
    protected static string $resource = BillOfLadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
