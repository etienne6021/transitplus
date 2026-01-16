<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToAgency;
use App\Traits\LogsActivityTrait;

class Dossier extends Model
{
    use BelongsToAgency, LogsActivityTrait;
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function declarations()
    {
        return $this->hasMany(Declaration::class);
    }

    public function entrees()
    {
        return $this->hasMany(Entree::class);
    }

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function billOfLadings()
    {
        return $this->hasMany(BillOfLading::class);
    }
}
