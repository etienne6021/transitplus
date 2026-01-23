<?php

namespace App\Models;

use App\Models\User;
use App\Traits\BelongsToAgency;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivityTrait;

class Product extends Model
{
    use BelongsToAgency, LogsActivityTrait;

    protected static function booted()
    {
        static::updated(function ($product) {
            if ($product->quantity <= $product->min_stock && $product->wasChanged('quantity')) {
                $recipients = User::where('agency_id', $product->agency_id)
                    ->get()
                    ->filter(fn ($user) => $user->can('gestion_stocks') || $user->hasRole('Super Admin'));

                \Filament\Notifications\Notification::make()
                    ->title('Alerte Stock Critique')
                    ->body("Le stock pour {$product->name} est bas ({$product->quantity} restants).")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->sendToDatabase($recipients);
            }
        });
    }

    protected $guarded = [];

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
