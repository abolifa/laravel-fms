<?php

namespace Database\Seeders;

use App\Models\Fuel;
use App\Models\MaintenanceType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = [
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'name' => 'admin'
        ];

        Fuel::insert([
            ['type' => 'بنزين'],
            ['type' => 'ديزل'],
            ['type' => 'قاز'],
        ]);

        User::insert($user);

        $maintenanceTypes = [
            ['name' => 'زيت محرك'],
            ['name' => 'فلتر زيت'],
            ['name' => 'فلتر هوء'],
            ['name' => 'شماعي'],
            ['name' => 'فلتر وقود'],
            ['name' => 'عجلات'],
            ['name' => 'باطنيات'],
            ['name' => 'شينقه'],
            ['name' => 'زيت كمبيو'],
            ['name' => 'كاتينة'],
            ['name' => 'شدادات'],
            ['name' => 'سمكرة'],
            ['name' => 'صيانة محرك'],
            ['name' => 'كهرباء'],
            ['name' => 'ميكانيكا'],
            ['name' => 'غير ذلك'],
        ];

        MaintenanceType::insert($maintenanceTypes);
    }
}
