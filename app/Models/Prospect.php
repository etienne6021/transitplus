<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

use App\Traits\LogsActivityTrait;

class Prospect extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function latestInteraction()
    {
        return $this->hasOne(Interaction::class)->latestOfMany();
    }
}
