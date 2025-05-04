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
        $client = Role::factory()->create([
            'name' => 'client',
            'guard_name' => 'auth',
            'description' => 'Client role',
        ])->givePermissionTo('client');

        Role::factory()->create([
            'name' => 'designer',
            'guard_name' => 'auth',
            'description' => 'Designer role',
        ])->givePermissionTo('designer');

        Role::factory()->create([
            'name' => 'support',
            'guard_name' => 'admin',
            'description' => 'Admin role',
        ]);

        Role::factory()->create([
            'name' => 'admin',
            'guard_name' => 'admin',
            'description' => 'Admin role',
        ]);
    }
}
