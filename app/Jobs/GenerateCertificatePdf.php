<?php

namespace App\Jobs;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class GenerateCertificatePdf implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $certificateId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $certificate = Certificate::with('registration.event')->find($this->certificateId);

        if (! $certificate) {
            return;
        }

        $registration = $certificate->registration;
        $event = $registration->event;

        $verificationUrl = URL::to('/sertifikat/verifikasi/'.$certificate->verification_token);
        $qrSvg = QrCode::size(160)->generate($verificationUrl);

        $pdf = Pdf::loadView('certificates.pdf', [
            'participantName' => $registration->name,
            'eventName' => $event->name,
            'eventStartsAt' => $event->starts_at,
            'eventEndsAt' => $event->ends_at,
            'certificateNumber' => $certificate->certificate_number,
            'qrSvg' => $qrSvg,
        ])->setPaper('a4', 'landscape');

        $path = 'certificates/'.$certificate->certificate_number.'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        $certificate->update([
            'file_path' => $path,
            'status' => CertificateStatus::Generated,
            'generated_at' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Gagal generate sertifikat', [
            'certificate_id' => $this->certificateId,
            'error' => $exception->getMessage(),
        ]);

        Certificate::where('id', $this->certificateId)->update([
            'status' => CertificateStatus::Failed,
        ]);
    }
}
