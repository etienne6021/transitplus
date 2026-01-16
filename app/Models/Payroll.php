<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivityTrait;

class Payroll extends Model
{
    use LogsActivityTrait;
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
