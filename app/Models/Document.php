<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

class Document extends Model
{
    use BelongsToAgency;
    protected $guarded = [];

    public function documentable()
    {
        return $this->morphTo();
    }
}
