<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use Filament\Forms\Form;
use Filament\Forms;
use App\Models\Agency;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class AgencyProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Paramètres Système';

    protected static ?string $title = 'Profil de l\'Agence';

    protected static ?string $navigationLabel = 'Profil Agence';

    protected static string $view = 'filament.pages.agency-profile';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $agency = auth()->user()->agency;
        
        if (!$agency) {
            abort(403);
        }

        $this->form->fill($agency->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité de l\'Agence')
                    ->description('Ces informations apparaîtront sur vos factures et documents officiels.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('agencies/logos')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'Agence')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('capital')
                            ->label('Capital Social')
                            ->numeric()
                            ->prefix('FCFA')
                            ->helperText('Sera affiché en bas de facture.'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email de contact'),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Téléphone de contact (Public)')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Coordonnées & Fiscalité')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse physique'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone'),
                        Forms\Components\TextInput::make('nif')
                            ->label('NIF (Numéro d\'Identification Fiscale)'),
                        Forms\Components\TextInput::make('rccm')
                            ->label('RCCM'),
                        Forms\Components\TextInput::make('website')
                            ->label('Site Web')
                            ->prefix('https://'),
                    ])->columns(2),

                Forms\Components\Section::make('Activation des Modules')
                    ->description('Gestion de vos fonctionnalités activées.')
                    ->schema([
                        Forms\Components\CheckboxList::make('modules')
                            ->options([
                                'transit' => '🚢 Transit / Douane',
                                'commerce' => '🛒 Commerce & Distribution',
                                'hr' => '👥 Ressources Humaines',
                                'finance' => '📈 Finance & Journal de Caisse',
                                'mad' => '📦 Entreposage (MAD)',
                            ])
                            ->columns(2)
                            ->disabled(!auth()->user()->hasRole('Super Admin')), // Seul le super admin active les modules
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer les modifications')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $agency = auth()->user()->agency;
        
        // Sécurité : Ne pas mettre à jour les modules si on n'est pas Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            unset($data['modules']);
        }

        $agency->update($data);

        Notification::make()
            ->title('Profil mis à jour avec succès !')
            ->success()
            ->send();
    }
}
