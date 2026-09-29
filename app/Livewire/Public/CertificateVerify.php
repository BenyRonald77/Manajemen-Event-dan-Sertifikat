<?php

namespace App\Livewire\Public;

use App\Models\Certificate;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CertificateVerify extends Component
{
    public ?Certificate $certificate = null;

    public function mount(string $token): void
    {
        $this->certificate = Certificate::with('registration.event')
            ->where('verification_token', $token)
            ->first();
    }

    public function render(): View
    {
        return view('livewire.public.certificate-verify')->layout('layouts.public');
    }
}
