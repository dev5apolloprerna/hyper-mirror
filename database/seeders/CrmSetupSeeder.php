<?php

namespace Database\Seeders;

use App\Models\CrmRole;
use App\Models\Showroom;
use Illuminate\Database\Seeder;

class CrmSetupSeeder extends Seeder
{
    public function run()
    {
        $roles = ['StoreManager', 'Measurement', 'Production', 'Dispatch', 'Fitting', 'Account'];

        foreach ($roles as $role) {
            CrmRole::firstOrCreate(
                ['slug' => strtolower($role)],
                ['strRole' => $role]
            );
        }

        Showroom::firstOrCreate(['strShowRoomName' => 'Main Showroom']);
        Showroom::firstOrCreate(['strShowRoomName' => 'Branch Showroom']);
    }
}
