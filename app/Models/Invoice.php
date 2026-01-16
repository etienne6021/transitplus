<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Invoice extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
