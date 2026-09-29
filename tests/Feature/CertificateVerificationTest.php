<?php

namespace Tests\Feature;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_generated_certificate_shows_as_authentic(): void
    {
        $registration = Registration::factory()->checkedIn()->create(['name' => 'Nadia Ramadhani']);

        $certificate = Certificate::factory()
            ->for($registration, 'registration')
            ->generated()
            ->create(['certificate_number' => 'CERT-2026-000001']);

        $response = $this->get(route('certificates.verify', $certificate->verification_token));

        $response->assertOk();
        $response->assertSee('ASLI dan valid');
        $response->assertSee('Nadia Ramadhani');
        $response->assertSee('CERT-2026-000001');
    }

    public function test_an_unknown_token_is_reported_as_not_found_without_faking_success(): void
    {
        $response = $this->get(route('certificates.verify', 'token-yang-tidak-pernah-ada'));

        $response->assertOk();
        $response->assertSee('Sertifikat tidak ditemukan');
        $response->assertDontSee('ASLI dan valid');
    }

    public function test_a_pending_certificate_is_not_shown_as_authentic(): void
    {
        $registration = Registration::factory()->checkedIn()->create();

        $certificate = Certificate::factory()
            ->for($registration, 'registration')
            ->create(['status' => CertificateStatus::Pending]);

        $response = $this->get(route('certificates.verify', $certificate->verification_token));

        $response->assertOk();
        $response->assertDontSee('ASLI dan valid');
        $response->assertSee('belum tersedia');
    }

    public function test_download_is_refused_for_an_invalid_token(): void
    {
        $response = $this->get(route('certificates.download', 'token-palsu'));

        $response->assertNotFound();
    }

    public function test_download_returns_the_pdf_for_a_generated_certificate(): void
    {
        Storage::fake('local');

        $registration = Registration::factory()->checkedIn()->create();

        $certificate = Certificate::factory()
            ->for($registration, 'registration')
            ->generated()
            ->create();

        Storage::disk('local')->put($certificate->file_path, '%PDF-1.7 dummy');

        $response = $this->get(route('certificates.download', $certificate->verification_token));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
