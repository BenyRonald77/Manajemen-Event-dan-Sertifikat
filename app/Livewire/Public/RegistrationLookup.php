<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationLookup extends Component
{
    public string $email = '';

    public string $registration_code = '';

    public bool $searched = false;

    public ?Registration $result = null;

    public string $qrSvg = '';

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'registration_code' => ['required', 'string'],
        ];
    }

    public function search(): void
    {
        $this->validate();
        $this->searched = true;

        $this->result = Registration::with('event')
            ->where('email', $this->email)
            ->where('registration_code', strtoupper(trim($this->registration_code)))
            ->first();

        $this->qrSvg = $this->result
            ? QrCode::size(220)->generate($this->result->qr_payload)
            : '';
    }

    public function render(): View
    {
        return view('livewire.public.registration-lookup')->layout('layouts.public');
    }
}
