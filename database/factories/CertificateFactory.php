<?php

namespace Database\Factories;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory()->checkedIn(),
            'certificate_number' => Certificate::generateUniqueNumber(),
            'verification_token' => Certificate::generateUniqueToken(),
            'file_path' => null,
            'status' => CertificateStatus::Pending,
            'generated_at' => null,
        ];
    }

    public function generated(): static
    {
        return $this->state(fn () => [
            'status' => CertificateStatus::Generated,
            'file_path' => 'certificates/'.fake()->uuid().'.pdf',
            'generated_at' => now(),
        ]);
    }
}
