<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class FinancialTransaction extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
