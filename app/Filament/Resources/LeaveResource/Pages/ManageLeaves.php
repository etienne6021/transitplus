<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaves extends ManageRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
