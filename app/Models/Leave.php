<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Leave extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($leave) {
            $recipients = User::where('agency_id', $leave->agency_id)
                ->get()
                ->filter(fn ($user) => $user->can('gestion_conges') || $user->hasRole('Super Admin'));

            \Filament\Notifications\Notification::make()
                ->title('Nouvelle demande de congé')
                ->body("L'employé {$leave->employee->first_name} {$leave->employee->last_name} a soumis une demande.")
                ->icon('heroicon-o-calendar')
                ->color('primary')
                ->sendToDatabase($recipients);
        });
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
