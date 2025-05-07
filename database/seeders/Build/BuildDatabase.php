<?php

namespace Database\Seeders\Build;

use App\Models\User;
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
            PermissionSeeder::class,
            CategorySeeder::class,
            RolesSeeder::class,
            SkillSeeder::class,
        ]);
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com'
        ]);
        $user->assignRole('admin');
    }
}
