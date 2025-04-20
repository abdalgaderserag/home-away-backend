<?php

namespace Database\Factories;

use App\Enum\Offer\OfferType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'project_id' => \App\Models\Project::factory(),
            'price' => fake()->randomFloat(2, 1000, 100000),
            'deadline' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'start_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(OfferType::cases()),
            'expire_date' => fake()->dateTimeBetween('+3 months', '+6 months'),
        ];
    }
}
