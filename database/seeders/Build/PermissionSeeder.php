<?php

namespace Database\Seeders\Build;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'verified user']);
        Permission::create(['name' => 'client access']);
        Permission::create(['name' => 'designer access']);
        Permission::create(['name' => 'super access']);
        Permission::create(['name' => 'monitoring app']);
        Permission::create(['name' => 'edit locations']);
        Permission::create(['name' => 'edit skills']);
        Permission::create(['name' => 'edit unite types']);
        Permission::create(['name' => 'edit faq']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'edit verification']);
        Permission::create(['name' => 'mail access']);
        Permission::create(['name' => 'edit role and permissions']);
        Permission::create(['name' => 'edit categories']);
    }
}
