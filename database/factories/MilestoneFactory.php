<?php

namespace Database\Factories;

use App\Enum\Offer\MilestoneStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => \App\Models\Offer::factory(),
            'status' => fake()->randomElement(MilestoneStatus::cases()),
            'deadline' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'delivery_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'attachments' => json_encode([fake()->imageUrl(), fake()->imageUrl()]),
            'price' => fake()->randomFloat(2, 1000, 10000),
            'description' => fake()->paragraph(),
        ];
    }
}
