<?php

namespace Database\Seeders\Build;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::factory()->make([
            'city' => 'cairo',
        ])->save();
        Location::factory()
            ->count(10)
            ->create();
    }
}
