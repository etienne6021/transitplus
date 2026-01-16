<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Interaction extends Model
{
    use BelongsToAgency, LogsActivityTrait;

    protected $guarded = [];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}
