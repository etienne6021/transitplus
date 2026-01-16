<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

use App\Traits\LogsActivityTrait;

class Client extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
