<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Document extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    public function documentable()
    {
        return $this->morphTo();
    }
}
