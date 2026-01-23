<?php

namespace App\Filament\Resources\MailRecordResource\Pages;

use App\Filament\Resources\MailRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMailRecords extends ManageRecords
{
    protected static string $resource = MailRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
