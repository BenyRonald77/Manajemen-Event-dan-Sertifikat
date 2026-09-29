<?php

namespace Tests\Feature;

use App\Enums\CertificateStatus;
use App\Jobs\GenerateCertificatePdf;
use App\Livewire\Admin\EventShow;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class CertificateGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generation_only_dispatches_jobs_for_checked_in_registrations(): void
    {
        Bus::fake();

        $this->actingAs(User::factory()->create());

        $event = Event::factory()->create();

        $checkedIn = Registration::factory()->for($event)->checkedIn()->count(3)->create();
        $notCheckedIn = Registration::factory()->for($event)->count(2)->create();

        Livewire::test(EventShow::class, ['event' => $event])
            ->call('generateCertificates');

        // Hanya 3 sertifikat (untuk yang checked-in) yang dibuat, bukan 5.
        $this->assertSame(3, Certificate::count());

        foreach ($checkedIn as $registration) {
            $this->assertTrue($registration->refresh()->certificate()->exists());
        }

        foreach ($notCheckedIn as $registration) {
            $this->assertFalse($registration->refresh()->certificate()->exists());
        }

        Bus::assertBatched(function ($batch) {
            return $batch->jobs->count() === 3;
        });
    }

    public function test_generation_is_skipped_when_no_checked_in_registration_needs_a_certificate(): void
    {
        Bus::fake();

        $this->actingAs(User::factory()->create());

        $event = Event::factory()->create();
        Registration::factory()->for($event)->count(2)->create(); // belum check-in

        $component = Livewire::test(EventShow::class, ['event' => $event])
            ->call('generateCertificates');

        $this->assertNotEmpty($component->get('generateMessage'));
        $this->assertSame(0, Certificate::count());
        Bus::assertNothingBatched();
    }

    public function test_certificates_start_as_pending_until_the_queued_job_runs(): void
    {
        // Bus::fake() mencegah job benar-benar dieksekusi, sehingga kita bisa
        // membuktikan sertifikat dibuat berstatus "pending" dulu (baris data
        // dan pengiriman ke antrean terjadi sebelum PDF benar-benar dirender).
        Bus::fake();

        $this->actingAs(User::factory()->create());

        $event = Event::factory()->create();
        $registration = Registration::factory()->for($event)->checkedIn()->create();

        Livewire::test(EventShow::class, ['event' => $event])
            ->call('generateCertificates');

        $certificate = Certificate::first();
        $this->assertSame(CertificateStatus::Pending, $certificate->status);
        $this->assertNull($certificate->file_path);

        // Setelah job benar-benar dijalankan oleh queue worker (disimulasikan
        // langsung di sini), sertifikat berubah menjadi generated dan
        // filenya ada.
        (new GenerateCertificatePdf($certificate->id))->handle();

        $certificate->refresh();
        $this->assertSame(CertificateStatus::Generated, $certificate->status);
        $this->assertNotNull($certificate->file_path);
        $this->assertTrue($certificate->fileExists());
    }
}
