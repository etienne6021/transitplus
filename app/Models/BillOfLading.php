<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

class BillOfLading extends Model
{
    use BelongsToAgency;
    protected $guarded = [];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function ship()
    {
        return $this->belongsTo(Ship::class);
    }
}
