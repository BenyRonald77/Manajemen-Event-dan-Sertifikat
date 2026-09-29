<div @if($batch && !$batch->finished()) wire:poll.2s @endif>
    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div>
                <a href="{{ route('panitia.events.index') }}" wire:navigate class="text-sm text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar acara</a>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $event->starts_at->translatedFormat('d M Y, H:i') }} &mdash; {{ $event->ends_at->translatedFormat('d M Y, H:i') }}
                    @if ($event->location)
                        &middot; {{ $event->location }}
                    @endif
                </p>
                @if ($event->description)
                    <p class="mt-2 text-sm text-slate-600">{{ $event->description }}</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-500">Total pendaftar</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $registrations->count() }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-500">Sudah check-in</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ $checkedInCount }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-500">Sertifikat terbit</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $certificates->where('status', \App\Enums\CertificateStatus::Generated)->count() }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-500">Kuota</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $event->quota ?? 'Tidak terbatas' }}</p>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Generate sertifikat</h3>
                        <p class="mt-1 text-sm text-slate-600">Hanya peserta yang sudah check-in yang akan dibuatkan sertifikat.</p>
                    </div>

                    @if (! $batch || $batch->finished())
                        <button
                            type="button"
                            wire:click="generateCertificates"
                            wire:loading.attr="disabled"
                            wire:target="generateCertificates"
                            @if ($pendingCertificateCount === 0) disabled @endif
                            class="shrink-0 rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                        >
                            <span wire:loading.remove wire:target="generateCertificates">Generate Sertifikat ({{ $pendingCertificateCount }})</span>
                            <span wire:loading wire:target="generateCertificates">Memulai...</span>
                        </button>
                    @endif
                </div>

                @if ($generateMessage)
                    <p class="mt-3 text-sm text-amber-700">{{ $generateMessage }}</p>
                @endif

                @if ($batch)
                    <div class="mt-4 rounded-md border border-slate-200 bg-slate-50 p-4">
                        @if ($batch->finished())
                            <p class="text-sm font-medium text-slate-700">
                                Batch terakhir selesai: {{ $batch->processedJobs() }}/{{ $batch->totalJobs }} sertifikat.
                                @if ($batch->hasFailures())
                                    <span class="text-red-600">{{ $batch->failedJobs }} gagal.</span>
                                @endif
                            </p>
                        @else
                            <p class="text-sm font-medium text-slate-700">
                                Sedang memproses: {{ $batch->processedJobs() }}/{{ $batch->totalJobs }} sertifikat selesai
                                @if ($batch->failedJobs > 0)
                                    <span class="text-red-600">({{ $batch->failedJobs }} gagal)</span>
                                @endif
                            </p>
                            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-2 rounded-full bg-teal-600 transition-all"
                                    style="width: {{ $batch->totalJobs > 0 ? round(($batch->processedJobs() / $batch->totalJobs) * 100) : 0 }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Halaman ini memperbarui progres secara otomatis. Pastikan <code>php artisan queue:work</code> sedang berjalan.</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-200 p-4">
                    <h3 class="text-base font-semibold text-slate-900">Pendaftar</h3>
                </div>

                @if ($registrations->isEmpty())
                    <p class="p-6 text-sm text-slate-600">Belum ada pendaftar untuk acara ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-slate-200 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Sertifikat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($registrations as $registration)
                                    @php $certificate = $certificates->get($registration->id); @endphp
                                    <tr>
                                        <td class="px-4 py-2 font-medium text-slate-900">{{ $registration->name }}</td>
                                        <td class="px-4 py-2 text-slate-600">{{ $registration->email }}</td>
                                        <td class="px-4 py-2">
                                            @if ($registration->isCheckedIn())
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Check-in</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Terdaftar</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if (! $certificate)
                                                <span class="text-xs text-slate-400">&mdash;</span>
                                            @elseif ($certificate->isGenerated())
                                                <a href="{{ route('certificates.download', $certificate->verification_token) }}" class="text-teal-700 hover:text-teal-800">
                                                    Unduh {{ $certificate->certificate_number }}
                                                </a>
                                            @elseif ($certificate->status === \App\Enums\CertificateStatus::Failed)
                                                <span class="text-xs font-medium text-red-600">Gagal dibuat</span>
                                            @else
                                                <span class="text-xs font-medium text-amber-600">Sedang diproses...</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
