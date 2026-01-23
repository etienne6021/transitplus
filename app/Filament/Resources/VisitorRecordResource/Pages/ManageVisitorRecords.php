<?php

namespace App\Filament\Resources\VisitorRecordResource\Pages;

use App\Filament\Resources\VisitorRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVisitorRecords extends ManageRecords
{
    protected static string $resource = VisitorRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
