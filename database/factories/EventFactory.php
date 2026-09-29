<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-2 months', '+2 months');

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'location' => fake()->address(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+3 hours'),
            'quota' => fake()->boolean(70) ? fake()->numberBetween(20, 300) : null,
        ];
    }
}
