<?php

namespace App\Models;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use BelongsToAgency, LogsActivityTrait;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
