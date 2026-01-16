<?php

namespace App\Filament\Resources\CustomsOfficeResource\Pages;

use App\Filament\Resources\CustomsOfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomsOffices extends ManageRecords
{
    protected static string $resource = CustomsOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
