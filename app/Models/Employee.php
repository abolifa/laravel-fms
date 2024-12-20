<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
