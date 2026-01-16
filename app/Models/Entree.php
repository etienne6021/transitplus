<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivityTrait;

class Entree extends Model
{
    use LogsActivityTrait;
    protected $guarded = [];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function sorties()
    {
        return $this->hasMany(Sortie::class);
    }
}
