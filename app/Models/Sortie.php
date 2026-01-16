<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Sortie extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($sortie) {
            $sortie->updateEntreeStatus();
        });

        static::deleted(function ($sortie) {
            $sortie->updateEntreeStatus();
        });
    }

    public function updateEntreeStatus()
    {
        $entree = $this->entree;
        if (!$entree) return;

        $totalSortis = $entree->sorties()->sum('nombre_colis_sortis');
        
        if ($totalSortis >= $entree->nombre_colis) {
            $entree->update(['statut' => 'Sorti']);
        } elseif ($totalSortis > 0) {
            $entree->update(['statut' => 'Reçu']); // Ou 'En cours de sortie'
        } else {
            $entree->update(['statut' => 'Reçu']);
        }
    }

    public function entree()
    {
        return $this->belongsTo(Entree::class);
    }
}
