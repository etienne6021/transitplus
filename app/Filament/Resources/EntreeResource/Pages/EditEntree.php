<?php

namespace App\Filament\Resources\EntreeResource\Pages;

use App\Filament\Resources\EntreeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntree extends EditRecord
{
    protected static string $resource = EntreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
