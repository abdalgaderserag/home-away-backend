<?php

namespace Database\Factories;

use App\Enum\Offer\MilestoneStatus;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *offer_id
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::all()->random()->id,
            'status' => fake()->randomElement(MilestoneStatus::cases()),
            'deadline' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'delivery_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'price' => fake()->randomFloat(2, 1000, 10000),
            'description' => fake()->paragraph(),
        ];
    }
}
