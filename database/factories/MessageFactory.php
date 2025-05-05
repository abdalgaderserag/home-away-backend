<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => \App\Models\User::factory(),
            'receiver_id' => \App\Models\User::factory(),
            'context' => fake()->optional()->paragraph(),
            'attachment' => fake()->optional()->randomElements([
                ['url' => fake()->imageUrl()],
                ['document' => fake()->filePath()],
                [fake()->imageUrl(), fake()->filePath()]
            ]),
        ];
    }
}
