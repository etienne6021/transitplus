<?php

namespace App\Filament\Resources\DossierResource\Pages;

use App\Filament\Resources\DossierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDossiers extends ListRecords
{
    protected static string $resource = DossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_sydonia')
                ->label('Importer depuis Sydonia (XML)')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('sydonia_xml')
                        ->label('Fichier XML Sydonia (SAD)')
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data, \App\Services\SydoniaXmlService $service) {
                    $fileContent = $data['sydonia_xml']->get();
                    try {
                        $declaration = $service->import($fileContent);
                        \Filament\Notifications\Notification::make()
                            ->title('Sydonia Importé')
                            ->body("Dossier et Déclaration n° {$declaration->numero_sydonia} créés avec succès.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur d\'importation')
                            ->body("Le fichier n'est pas au format SAD standard : " . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make()
                ->label('Nouveau Dossier'),
        ];
    }
}
