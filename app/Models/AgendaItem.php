<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class AgendaItem extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    
    protected static function booted()
    {
        static::creating(function ($agendaItem) {
            if (!$agendaItem->user_id && auth()->check()) {
                $agendaItem->user_id = auth()->id();
            }
        });

        static::created(function ($agendaItem) {
            // Si l'auteur n'est pas le bénéficiaire (ex: secrétaire qui crée pour le PDG)
            if (auth()->check() && auth()->id() !== $agendaItem->user_id) {
                $recipient = User::find($agendaItem->user_id);
                if ($recipient) {
                    \Filament\Notifications\Notification::make()
                        ->title('Nouvel événement ajouté à votre agenda')
                        ->body("Un événement a été ajouté par " . auth()->user()->name . " : " . $agendaItem->title)
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->sendToDatabase($recipient);
                }
            }
        });
    }

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
