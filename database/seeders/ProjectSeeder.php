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

        Project::factory()->count(20)->create([
            'client_id' => fn() => $users->random()->id,
            'designer_id' => fn() => $users->where('id', '!=', request('client_id'))->random()->id,
        ]);
    }
}