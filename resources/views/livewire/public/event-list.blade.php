<div>
    <h1 class="text-2xl font-semibold text-slate-900">Acara yang bisa didaftar</h1>
    <p class="mt-1 text-sm text-slate-600">Pilih acara di bawah untuk mendaftar. Anda akan menerima kode dan QR pendaftaran setelah mengisi formulir.</p>

    @if ($events->isEmpty())
        <div class="mt-8 rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
            <p class="text-sm text-slate-600">Belum ada acara yang dibuka untuk pendaftaran saat ini. Silakan cek kembali nanti.</p>
        </div>
    @else
        <ul class="mt-8 space-y-4">
            @foreach ($events as $event)
                <li class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $event->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $event->starts_at->translatedFormat('d M Y, H:i') }}
                                hingga
                                {{ $event->ends_at->translatedFormat('d M Y, H:i') }}
                            </p>
                            @if ($event->location)
                                <p class="mt-1 text-sm text-slate-500">{{ $event->location }}</p>
                            @endif
                            @if ($event->quota !== null)
                                <p class="mt-2 text-xs font-medium {{ $event->isFull() ? 'text-red-600' : 'text-slate-500' }}">
                                    @if ($event->isFull())
                                        Kuota penuh
                                    @else
                                        Sisa kuota: {{ $event->remainingQuota() }} dari {{ $event->quota }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <a
                            href="{{ route('events.register', $event) }}"
                            wire:navigate
                            class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-700 min-h-[44px] px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                        >
                            Daftar acara ini
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
