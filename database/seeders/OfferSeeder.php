<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run()
    {
        $projects = Project::all();
        $users = User::all();

        Offer::factory()->count(50)->create([
            'project_id' => fn() => $projects->random()->id,
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
