<?php

namespace App\Models;

use App\Traits\BelongsToAgency;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivityTrait;

class Product extends Model
{
    use BelongsToAgency, LogsActivityTrait;

    protected $guarded = [];

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
