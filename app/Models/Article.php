<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

class Article extends Model
{
    use BelongsToAgency;
    protected $guarded = [];

    public function declaration()
    {
        return $this->belongsTo(Declaration::class);
    }
}
