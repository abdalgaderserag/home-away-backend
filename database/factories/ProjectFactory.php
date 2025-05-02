<?php

namespace Database\Factories;

use App\Enum\Project\Status;
use App\Enum\Project\UnitType;
use App\Enum\Project\Location;
use App\Enum\Project\Skill;
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
            // 'client_id' => \App\Models\User::factory(),
            // 'designer_id' => \App\Models\User::factory()->nullable(),
            'status' => fake()->randomElement(Status::cases()),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'unit_type' => fake()->randomElement(UnitType::cases()),
            'space' => fake()->numberBetween(50, 500),
            'location' => \App\Models\Location::factory(),
            'deadline' => fake()->dateTimeBetween('+1 month', '+1 year'),
            'min_price' => fake()->randomFloat(2, 10000, 50000),
            'max_price' => fake()->randomFloat(2, 50000, 100000),
            'resources' => fake()->boolean(),
            'skill' => fake()->randomElement(Skill::cases()),
            'attachments' => json_encode([fake()->imageUrl(), fake()->imageUrl()]),
        ];
    }
}
