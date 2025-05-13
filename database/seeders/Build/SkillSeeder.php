<?php

namespace Database\Seeders\Build;

use App\Models\Skill;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //'construction', 'design', 'renovation'
        Skill::create([
            'name' => 'construction',
        ]);
        Skill::create([
            'name' => 'design',
        ]);
        Skill::create([
            'name' => 'renovation',
        ]);

        UnitType::create([
            'type' => 'house',
        ]);
    }
}
