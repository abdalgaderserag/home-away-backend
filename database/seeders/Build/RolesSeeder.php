<?php

namespace Database\Seeders\Build;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = Role::create([
            'name' => 'client',
            'guard_name' => 'web',
        ])->givePermissionTo('client');

        Role::create([
            'name' => 'designer',
            'guard_name' => 'web',
        ])->givePermissionTo('designer');

        Role::create([
            'name' => 'support',
            'guard_name' => 'web',
        ])->givePermissionTo(['edit locations', 'edit users', 'verify edit', 'edit bio']);

        Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ])->givePermissionTo(['super access']);
    }
}
