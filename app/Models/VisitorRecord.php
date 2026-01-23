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
}
