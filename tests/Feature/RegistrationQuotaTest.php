<?php

namespace Tests\Feature;

use App\Livewire\Public\EventRegister;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_succeeds_while_quota_is_available(): void
    {
        $event = Event::factory()->create(['quota' => 2]);

        Livewire::test(EventRegister::class, ['event' => $event])
            ->set('name', 'Peserta Satu')
            ->set('email', 'satu@example.test')
            ->set('phone', '081200000001')
            ->call('register')
            ->assertHasNoErrors();

        $this->assertSame(1, Registration::where('event_id', $event->id)->count());
    }

    public function test_registration_is_rejected_once_quota_is_full(): void
    {
        $event = Event::factory()->create(['quota' => 2]);

        Registration::factory()->for($event)->create(['email' => 'a@example.test']);
        Registration::factory()->for($event)->create(['email' => 'b@example.test']);

        $component = Livewire::test(EventRegister::class, ['event' => $event])
            ->set('name', 'Peserta Ketiga')
            ->set('email', 'tiga@example.test')
            ->set('phone', '081200000003')
            ->call('register');

        $component->assertSet('quotaFull', true);

        // Tidak ada baris baru yang tersimpan, kuota tetap 2.
        $this->assertSame(2, Registration::where('event_id', $event->id)->count());
        $this->assertFalse(Registration::where('email', 'tiga@example.test')->exists());
    }

    public function test_event_without_quota_never_reports_full(): void
    {
        $event = Event::factory()->create(['quota' => null]);

        Registration::factory()->count(5)->for($event)->create();

        $this->assertFalse($event->isFull());
    }
}
