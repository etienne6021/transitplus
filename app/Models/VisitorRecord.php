<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class VisitorRecord extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];
    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($visitor) {
            if ($visitor->visited_user_id) {
                $recipient = User::find($visitor->visited_user_id);
                if ($recipient) {
                    \Filament\Notifications\Notification::make()
                        ->title('Un visiteur vous attend')
                        ->body("{$visitor->visitor_name} est arrivé pour vous voir.")
                        ->icon('heroicon-o-user-group')
                        ->color('info')
                        ->sendToDatabase($recipient);
                }
            }
        });
    }

    public function visitedUser()
    {
        return $this->belongsTo(User::class, 'visited_user_id');
    }
}
