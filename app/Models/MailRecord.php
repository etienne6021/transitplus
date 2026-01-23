<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class MailRecord extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($mail) {
            if ($mail->statut === 'Urgent') {
                $recipients = User::where('agency_id', $mail->agency_id)
                    ->get()
                    ->filter(fn ($user) => $user->can('gestion_courrier') || $user->hasRole('Super Admin'));

                \Filament\Notifications\Notification::make()
                    ->title('Courrier URGENT reçu')
                    ->body("Un nouveau courrier urgent a été enregistré : {$mail->subject}")
                    ->icon('heroicon-o-envelope-open')
                    ->color('danger')
                    ->sendToDatabase($recipients);
            }
        });
    }
}
