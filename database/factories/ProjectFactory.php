<?php

namespace Database\Factories;

use App\Enum\Project\Status;
use App\Models\Location;
use App\Models\Skill;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Status::cases()),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'unit_type_id' => UnitType::all()->random()->id,
            'space' => fake()->numberBetween(100, 500),
            'location_id' => Location::all()->random()->id,
            'deadline' => fake()->dateTimeBetween('+1 month', '+1 year'),
            'min_price' => fake()->randomFloat(2, 10000, 50000),
            'max_price' => fake()->randomFloat(2, 50000, 100000),
            'resources' => fake()->boolean(),
            'skill_id' => Skill::all()->random()->id,
            'published_at' => now(),
        ];
    }
}
