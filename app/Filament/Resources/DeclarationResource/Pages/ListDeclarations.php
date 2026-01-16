<?php

namespace App\Filament\Resources\DeclarationResource\Pages;

use App\Filament\Resources\DeclarationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeclarations extends ListRecords
{
    protected static string $resource = DeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_sydonia')
                ->label('Importer Sydonia (XML)')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('sydonia_xml')
                        ->label('Fichier XML Sydonia')
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data, \App\Services\SydoniaXmlService $service) {
                    $fileContent = $data['sydonia_xml']->get();
                    $declaration = $service->import($fileContent);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Import réussi')
                        ->body("La déclaration n° {$declaration->numero_sydonia} et ses articles ont été importés avec succès.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
