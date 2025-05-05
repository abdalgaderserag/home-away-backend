<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Build\BuildDatabase;
use Google\Service\ApigeeRegistry\Build;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        /*$this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            OfferSeeder::class,
            MilestoneSeeder::class,
            MessageSeeder::class,
            RateSeeder::class,
        ]);*/
        $this->call([
            BuildDatabase::class
        ]);
    }
}
