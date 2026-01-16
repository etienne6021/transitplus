<?php

namespace App\Filament\Resources\CustomsRegimeResource\Pages;

use App\Filament\Resources\CustomsRegimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomsRegimes extends ManageRecords
{
    protected static string $resource = CustomsRegimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
