<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    public function run()
    {
        Project::all()->each(function ($project) {
            Rate::factory()->create([
                'project_id' => $project->id,
                'client_id' => $project->client_id,
                'designer_id' => $project->designer_id
            ]);
        });
    }
}
