<?php

namespace App\Traits;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToAgency
{
    protected static function bootBelongsToAgency()
    {
        static::creating(function (Model $model) {
            if (auth()->check() && ! $model->agency_id) {
                $model->agency_id = auth()->user()->agency_id;
            }
        });

        static::addGlobalScope('agency', function (Builder $builder) {
            //hasUser avoids infinite loops during initial authentication
            if (auth()->hasUser()) {
                $user = auth()->user();
                if (!$user->hasRole('Super Admin')) {
                    $builder->where($builder->getQuery()->from . '.agency_id', $user->agency_id);
                }
            }
        });
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
