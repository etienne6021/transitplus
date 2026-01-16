<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $guarded = [];

    protected $casts = [
        'modules' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
