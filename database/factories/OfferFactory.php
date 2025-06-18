<?php

namespace Database\Factories;

use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *user_id
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'project_id' => \App\Models\Project::factory(),
            'price' => fake()->randomFloat(2, 1000, 100000),
            'status' => OfferStatus::Pending,
            'deadline' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'start_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'description' => fake()->paragraph(),
            'type' => OfferType::Basic,
            'expire_date' => fake()->dateTimeBetween('+3 months', '+6 months'),
        ];
    }
}
