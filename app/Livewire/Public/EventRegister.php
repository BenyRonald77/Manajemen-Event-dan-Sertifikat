<?php

namespace App\Livewire\Public;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EventRegister extends Component
{
    public Event $event;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public bool $quotaFull = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->quotaFull = $event->isFull();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('registrations', 'email')->where('event_id', $this->event->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar untuk acara ini. Gunakan halaman "cek status pendaftaran" untuk melihat QR Anda.',
        ];
    }

    public function register()
    {
        $this->event->refresh();

        if ($this->event->isFull()) {
            $this->quotaFull = true;

            return;
        }

        $validated = $this->validate();

        $registration = DB::transaction(function () use ($validated) {
            $this->event->refresh();

            if ($this->event->isFull()) {
                $this->quotaFull = true;

                return null;
            }

            $code = Registration::generateUniqueCode();

            return Registration::create([
                'event_id' => $this->event->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'registration_code' => $code,
                'qr_payload' => $code,
                'status' => RegistrationStatus::Registered,
            ]);
        });

        if ($registration === null) {
            return;
        }

        $this->redirectRoute('registrations.confirmation', $registration->registration_code, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.public.event-register')->layout('layouts.public');
    }
}
