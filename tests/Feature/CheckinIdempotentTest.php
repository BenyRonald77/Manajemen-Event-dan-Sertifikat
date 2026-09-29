<?php

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Livewire\Admin\CheckinScanner;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckinIdempotentTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanning_a_valid_code_marks_registration_as_checked_in(): void
    {
        $this->actingAs(User::factory()->create());

        $registration = Registration::factory()->create();

        Livewire::test(CheckinScanner::class)
            ->call('checkin', $registration->registration_code)
            ->assertSet('lastResult.type', 'success');

        $registration->refresh();
        $this->assertSame(RegistrationStatus::CheckedIn, $registration->status);
        $this->assertNotNull($registration->checked_in_at);
    }

    public function test_scanning_an_already_checked_in_code_is_idempotent(): void
    {
        $this->actingAs(User::factory()->create());

        $registration = Registration::factory()->checkedIn()->create();
        $originalCheckedInAt = $registration->checked_in_at;

        Livewire::test(CheckinScanner::class)
            ->call('checkin', $registration->registration_code)
            ->assertSet('lastResult.type', 'already');

        $registration->refresh();

        // Waktu check-in semula tidak berubah, tidak ditimpa oleh scan kedua.
        $this->assertTrue($originalCheckedInAt->equalTo($registration->checked_in_at));
    }

    public function test_scanning_an_unknown_code_reports_not_found_honestly(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CheckinScanner::class)
            ->call('checkin', 'TIDAKADA')
            ->assertSet('lastResult.type', 'not_found');
    }

    public function test_manual_fallback_input_produces_the_same_result_as_scanning(): void
    {
        $this->actingAs(User::factory()->create());

        $registration = Registration::factory()->create();

        Livewire::test(CheckinScanner::class)
            ->set('manualCode', $registration->registration_code)
            ->call('checkinManual')
            ->assertSet('lastResult.type', 'success');

        $this->assertTrue($registration->refresh()->isCheckedIn());
    }
}
