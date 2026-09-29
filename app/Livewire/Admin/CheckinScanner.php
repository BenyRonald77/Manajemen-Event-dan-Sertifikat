<?php

namespace App\Livewire\Admin;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class CheckinScanner extends Component
{
    public string $manualCode = '';

    /**
     * @var array{type: string, code?: string, registration?: Registration}|null
     */
    public ?array $lastResult = null;

    public function checkin(string $code): void
    {
        $this->processCode($code);
    }

    public function checkinManual(): void
    {
        $this->validate([
            'manualCode' => ['required', 'string', 'max:20'],
        ], [
            'manualCode.required' => 'Masukkan kode pendaftaran terlebih dahulu.',
        ]);

        $this->processCode($this->manualCode);
        $this->manualCode = '';
    }

    private function processCode(string $code): void
    {
        $code = strtoupper(trim($code));

        $registration = Registration::with('event')
            ->where('registration_code', $code)
            ->first();

        if (! $registration) {
            $this->lastResult = ['type' => 'not_found', 'code' => $code];

            return;
        }

        if ($registration->isCheckedIn()) {
            $this->lastResult = ['type' => 'already', 'registration' => $registration];

            return;
        }

        $registration->update([
            'status' => RegistrationStatus::CheckedIn,
            'checked_in_at' => now(),
        ]);

        $this->lastResult = ['type' => 'success', 'registration' => $registration->refresh()->load('event')];
    }

    public function render(): View
    {
        return view('livewire.admin.checkin-scanner')->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="text-xl font-semibold leading-tight text-slate-800">Scan Check-in</h2>'),
        ]);
    }
}
