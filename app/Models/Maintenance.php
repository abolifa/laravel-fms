<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $guarded = [];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function maintenanceTypes()
    {
        return $this->belongsToMany(MaintenanceType::class, 'maintenance_maintenance_type');
    }
}
