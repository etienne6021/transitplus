<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class InternalNote extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($note) {
            $recipients = User::where('agency_id', $note->agency_id)->get();

            \Filament\Notifications\Notification::make()
                ->title('Nouvelle Note de Service')
                ->body("Une nouvelle note a été publiée : {$note->title}")
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->sendToDatabase($recipients);
        });
    }
    
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
