<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToAgency;

class DossierType extends Model
{
    use BelongsToAgency;
    protected $guarded = [];
}
