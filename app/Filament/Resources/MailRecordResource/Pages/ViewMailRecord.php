<?php

namespace App\Filament\Resources\MailRecordResource\Pages;

use App\Filament\Resources\MailRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMailRecord extends ViewRecord
{
    protected static string $resource = MailRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
