<?php

namespace App\Filament\Resources\InternalNoteResource\Pages;

use App\Filament\Resources\InternalNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInternalNotes extends ManageRecords
{
    protected static string $resource = InternalNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
