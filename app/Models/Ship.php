<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

class Ship extends Model
{
    use BelongsToAgency;
    protected $guarded = [];

    public function billOfLadings()
    {
        return $this->hasMany(BillOfLading::class);
    }
}
