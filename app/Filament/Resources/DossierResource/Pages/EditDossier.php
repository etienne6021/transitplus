<?php

namespace App\Filament\Resources\DossierResource\Pages;

use App\Filament\Resources\DossierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDossier extends EditRecord
{
    protected static string $resource = DossierResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Général & Finance';
    }

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
                        $service->import($fileContent, $this->record);
                        \Filament\Notifications\Notification::make()
                            ->title('Sydonia Importé')
                            ->body("Les données Sydonia ont été liées à ce dossier.")
                            ->success()
                            ->send();
                        $this->refreshFormData(['*']);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur d\'importation')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
