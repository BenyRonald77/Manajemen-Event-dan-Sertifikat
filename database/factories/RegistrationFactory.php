<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = Registration::generateUniqueCode();

        return [
            'event_id' => Event::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'registration_code' => $code,
            'qr_payload' => $code,
            'status' => RegistrationStatus::Registered,
            'checked_in_at' => null,
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn () => [
            'status' => RegistrationStatus::CheckedIn,
            'checked_in_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
