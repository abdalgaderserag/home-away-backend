<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $cli = $users->random()->id;
        Project::factory()->count(20)->create([
            'client_id' => $cli,
            'designer_id' => $users->where('id', '!=', $cli)->random()->id,
        ]);
    }
}
