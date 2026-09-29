<?php

namespace App\Livewire\Admin;

use App\Enums\CertificateStatus;
use App\Enums\RegistrationStatus;
use App\Jobs\GenerateCertificatePdf;
use App\Models\Certificate;
use App\Models\Event;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class EventShow extends Component
{
    public Event $event;

    public ?string $generateMessage = null;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function generateCertificates(): void
    {
        $this->generateMessage = null;
        $this->event->refresh();

        $existingBatch = $this->event->certificateBatch();
        if ($existingBatch && ! $existingBatch->finished()) {
            return;
        }

        $registrations = $this->event->registrations()
            ->where('status', RegistrationStatus::CheckedIn)
            ->whereDoesntHave('certificate')
            ->get();

        if ($registrations->isEmpty()) {
            $this->generateMessage = 'Tidak ada peserta baru yang perlu dibuatkan sertifikat. Sertifikat hanya dibuat untuk peserta yang sudah check-in.';

            return;
        }

        $jobs = DB::transaction(function () use ($registrations) {
            return $registrations->map(function ($registration) {
                $certificate = Certificate::create([
                    'registration_id' => $registration->id,
                    'certificate_number' => Certificate::generateUniqueNumber(),
                    'verification_token' => Certificate::generateUniqueToken(),
                    'status' => CertificateStatus::Pending,
                ]);

                return new GenerateCertificatePdf($certificate->id);
            })->all();
        });

        $batch = Bus::batch($jobs)
            ->name('Generate sertifikat: '.$this->event->name)
            ->dispatch();

        $this->event->update(['certificate_batch_id' => $batch->id]);
        $this->event->refresh();
    }

    public function render(): View
    {
        $this->event->refresh();

        $registrations = $this->event->registrations()->orderBy('name')->get();
        $certificates = Certificate::query()
            ->whereIn('registration_id', $registrations->pluck('id'))
            ->with('registration')
            ->get()
            ->keyBy('registration_id');

        $checkedInCount = $registrations->where('status', RegistrationStatus::CheckedIn)->count();
        $pendingCertificateCount = $registrations->filter(function ($registration) use ($certificates) {
            return $registration->status === RegistrationStatus::CheckedIn && ! $certificates->has($registration->id);
        })->count();

        /** @var Batch|null $batch */
        $batch = $this->event->certificateBatch();

        return view('livewire.admin.event-show', [
            'registrations' => $registrations,
            'certificates' => $certificates,
            'checkedInCount' => $checkedInCount,
            'pendingCertificateCount' => $pendingCertificateCount,
            'batch' => $batch,
        ])->layout('layouts.app', [
            'header' => new HtmlString('<h2 class="text-xl font-semibold leading-tight text-slate-800">'.e($this->event->name).'</h2>'),
        ]);
    }
}
