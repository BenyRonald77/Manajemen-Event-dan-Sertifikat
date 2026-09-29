<div class="rounded-lg border border-slate-200 bg-white p-6 sm:p-8">
    <h2 class="text-xl font-semibold text-slate-900">{{ $registration->event->name }}</h2>
    <p class="mt-1 text-sm text-slate-600">
        {{ $registration->event->starts_at->translatedFormat('d M Y, H:i') }} &mdash; {{ $registration->event->ends_at->translatedFormat('d M Y, H:i') }}
    </p>
    @if ($registration->event->location)
        <p class="text-sm text-slate-500">{{ $registration->event->location }}</p>
    @endif

    <div class="mt-6 grid gap-6 sm:grid-cols-2 sm:items-center">
        <div>
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-slate-500">Nama</dt>
                    <dd class="font-medium text-slate-900">{{ $registration->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kode pendaftaran</dt>
                    <dd class="font-mono text-base font-semibold tracking-wider text-slate-900">{{ $registration->registration_code }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd>
                        @if ($registration->isCheckedIn())
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                Sudah check-in {{ $registration->checked_in_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                Belum check-in
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
        <div class="flex flex-col items-center justify-center rounded-lg border border-slate-200 bg-slate-50 p-4">
            <div class="h-[220px] w-[220px]">{!! $qrSvg !!}</div>
            <p class="mt-2 text-center text-xs text-slate-500">Tunjukkan QR ini saat check-in di lokasi acara</p>
        </div>
    </div>
</div>
