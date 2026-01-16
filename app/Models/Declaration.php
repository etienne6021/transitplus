<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

class Declaration extends Model
{
    use BelongsToAgency;
    protected $guarded = [];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
