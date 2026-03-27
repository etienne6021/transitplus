<?php

namespace App\Filament\Resources\CourrierResource\Pages;

use App\Filament\Resources\CourrierResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCourrier extends ViewRecord
{
    protected static string $resource = CourrierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
