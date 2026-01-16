<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;

use App\Traits\LogsActivityTrait;

class Quotation extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
