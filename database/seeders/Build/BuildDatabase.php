<?php

namespace Database\Seeders\Build;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildDatabase extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            PermissionSeeder::class,
            CategorySeeder::class,
        ]);
    }
}
