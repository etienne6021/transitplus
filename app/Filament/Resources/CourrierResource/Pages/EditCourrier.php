<?php

namespace App\Filament\Resources\CourrierResource\Pages;

use App\Filament\Resources\CourrierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourrier extends EditRecord
{
    protected static string $resource = CourrierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
