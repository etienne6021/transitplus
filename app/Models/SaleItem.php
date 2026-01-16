<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($item) {
            $item->product?->decrement('stock_quantity', $item->quantity);
        });

        static::updated(function ($item) {
            $diff = $item->quantity - $item->getOriginal('quantity');
            $item->product?->decrement('stock_quantity', $diff);
        });

        static::deleted(function ($item) {
            $item->product?->increment('stock_quantity', $item->quantity);
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
