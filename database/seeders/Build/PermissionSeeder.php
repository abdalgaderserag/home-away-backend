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
        Permission::create(['name' => 'client']);
        Permission::create(['name' => 'designer']);

        Permission::create(['name' => 'super access']);
        Permission::create(['name' => 'edit locations']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'verify edit']);
        Permission::create(['name' => 'edit bio']);
    }
}
