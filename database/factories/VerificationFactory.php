<?php

namespace Database\Factories;

use App\Enum\VerificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\verification>
 */
class VerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => rand(1, 10),
            'type' => fake()->randomElement(VerificationType::cases()),
            'verified' => fake()->boolean(),
        ];
    }
}
