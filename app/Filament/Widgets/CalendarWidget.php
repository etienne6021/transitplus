<?php

namespace App\Filament\Widgets;

use App\Models\AgendaItem;
use App\Filament\Resources\AgendaItemResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class CalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // On ne l'affiche pas sur le tableau de bord (Dashboard)
        return ! (request()->routeIs('filament.admin.pages.dashboard'));
    }

    public Model | string | null $model = AgendaItem::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return AgendaItem::query()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                      ->orWhere('is_public', true);
            })
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('start_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(fn (AgendaItem $event) => [
                'id' => $event->id,
                'title' => "[{$event->category}] {$event->title}",
                'start' => $event->start_time,
                'end' => $event->end_time,
                'color' => $event->color,
                'url' => AgendaItemResource::getUrl('index', ['record' => $event]), // Ouvrir l'édition
                'shouldOpenUrlInNewTab' => false,
            ])
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('Titre')
                ->required(),
            Forms\Components\DateTimePicker::make('start_time')
                ->label('Début')
                ->required()
                ->seconds(false),
            Forms\Components\DateTimePicker::make('end_time')
                ->label('Fin')
                ->seconds(false),
            Forms\Components\Select::make('category')
                ->label('Catégorie')
                ->options([
                    'Réunion' => 'Réunion',
                    'Appel' => 'Appel',
                    'Visite' => 'Visite Client',
                    'Tâche' => 'Tâche Administrative',
                ])
                ->required(),
            Forms\Components\Select::make('status')
                ->label('Statut')
                ->options([
                    'En attente' => 'En attente',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ])
                ->required()
                ->default('En attente'),
            Forms\Components\ColorPicker::make('color')
                ->label('Couleur')
                ->default('#3b82f6'),
            Forms\Components\Select::make('user_id')
                ->label('Pour')
                ->relationship('user', 'name')
                ->default(auth()->id())
                ->visible(fn () => auth()->user()->can('gestion_agenda_autres'))
                ->required()
                ->searchable(),
        ];
    }
}
