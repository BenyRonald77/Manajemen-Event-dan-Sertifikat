<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationConfirmation extends Component
{
    public ?Registration $registration = null;

    public string $qrSvg = '';

    public function mount(string $registration_code): void
    {
        $this->registration = Registration::with('event')
            ->where('registration_code', $registration_code)
            ->first();

        if ($this->registration) {
            $this->qrSvg = QrCode::size(220)->generate($this->registration->qr_payload);
        }
    }

    public function render(): View
    {
        return view('livewire.public.registration-confirmation')->layout('layouts.public');
    }
}
