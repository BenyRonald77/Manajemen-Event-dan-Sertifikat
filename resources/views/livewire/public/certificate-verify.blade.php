<div>
    <h1 class="text-2xl font-semibold text-slate-900">Verifikasi sertifikat</h1>

    @if (! $certificate)
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-6 text-center">
            <p class="text-base font-semibold text-red-800">Sertifikat tidak ditemukan</p>
            <p class="mt-2 text-sm text-red-700">Token verifikasi pada tautan ini tidak cocok dengan sertifikat mana pun di sistem kami. Sertifikat ini tidak dapat dianggap sah.</p>
        </div>
    @elseif (! $certificate->isGenerated())
        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-6 text-center">
            <p class="text-base font-semibold text-amber-800">Sertifikat belum tersedia</p>
            <p class="mt-2 text-sm text-amber-700">Token ini valid, tetapi berkas sertifikatnya masih diproses atau gagal dibuat. Coba periksa kembali beberapa saat lagi.</p>
        </div>
    @else
        <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-6">
            <p class="text-base font-semibold text-emerald-800">Sertifikat ini ASLI dan valid</p>
        </div>

        <div class="mt-4 rounded-lg border border-slate-200 bg-white p-6 sm:p-8">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Nama peserta</dt>
                    <dd class="text-base font-semibold text-slate-900">{{ $certificate->registration->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Acara</dt>
                    <dd class="font-medium text-slate-900">{{ $certificate->registration->event->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tanggal acara</dt>
                    <dd class="text-slate-700">
                        {{ $certificate->registration->event->starts_at->translatedFormat('d M Y') }}
                        &mdash; {{ $certificate->registration->event->ends_at->translatedFormat('d M Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Nomor sertifikat</dt>
                    <dd class="font-mono font-medium text-slate-900">{{ $certificate->certificate_number }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tanggal terbit</dt>
                    <dd class="text-slate-700">{{ $certificate->generated_at->translatedFormat('d M Y, H:i') }}</dd>
                </div>
            </dl>

            <a
                href="{{ route('certificates.download', $certificate->verification_token) }}"
                class="mt-6 inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
            >
                Unduh PDF
            </a>
        </div>
    @endif
</div>
