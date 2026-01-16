<?php

namespace App\Models;

use App\Traits\BelongsToAgency;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use BelongsToAgency;

    protected $guarded = [];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}
