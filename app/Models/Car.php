<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
