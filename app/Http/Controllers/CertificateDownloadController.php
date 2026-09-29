<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateDownloadController extends Controller
{
    /**
     * Unduh PDF sertifikat berdasarkan verification_token. Publik: tidak
     * perlu login, tapi hanya sertifikat yang sudah berstatus "generated"
     * dan filenya benar-benar ada di storage yang bisa diunduh.
     */
    public function __invoke(string $token): StreamedResponse
    {
        $certificate = Certificate::where('verification_token', $token)->first();

        if (! $certificate || ! $certificate->isGenerated() || ! $certificate->fileExists()) {
            abort(404, 'Sertifikat tidak ditemukan atau belum tersedia untuk diunduh.');
        }

        return Storage::disk('local')->download(
            $certificate->file_path,
            $certificate->certificate_number.'.pdf'
        );
    }
}
