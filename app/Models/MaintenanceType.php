<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenanceTypeFactory> */
    use HasFactory;

    public function maintenances()
    {
        return $this->belongsToMany(Maintenance::class, 'maintenance_maintenance_type');
    }
}
