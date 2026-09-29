<div>
    <div class="py-8">
        <div class="mx-auto max-w-2xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-medium text-slate-700">Pindai QR peserta</h3>
                <p class="mt-1 text-xs text-slate-500">Arahkan kamera ke QR pada halaman konfirmasi pendaftaran peserta.</p>

                <div wire:ignore class="mt-4">
                    <div id="qr-reader" class="w-full overflow-hidden rounded-md border border-slate-200"></div>
                    <p id="qr-reader-status" class="mt-2 text-sm text-slate-500">Menyiapkan kamera...</p>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-medium text-slate-700">Atau masukkan kode secara manual</h3>
                <p class="mt-1 text-xs text-slate-500">Gunakan ini jika kamera tidak tersedia atau tidak diizinkan oleh perangkat.</p>

                <form wire:submit="checkinManual" class="mt-3 flex gap-2">
                    <label for="manual-code" class="sr-only">Kode pendaftaran</label>
                    <input
                        type="text"
                        id="manual-code"
                        wire:model="manualCode"
                        placeholder="Contoh: A1B2C3D4"
                        class="block w-full rounded-md border-slate-300 font-mono uppercase shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                    >
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="checkinManual"
                        class="inline-flex shrink-0 items-center justify-center rounded-md bg-teal-700 min-h-[44px] px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                    >
                        Check-in
                    </button>
                </form>
                @error('manualCode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if ($lastResult)
                <div
                    @class([
                        'rounded-lg border p-4',
                        'border-emerald-200 bg-emerald-50' => $lastResult['type'] === 'success',
                        'border-amber-200 bg-amber-50' => $lastResult['type'] === 'already',
                        'border-red-200 bg-red-50' => $lastResult['type'] === 'not_found',
                    ])
                >
                    @if ($lastResult['type'] === 'success')
                        <p class="text-sm font-medium text-emerald-800">Check-in berhasil</p>
                        <p class="mt-1 text-sm text-emerald-700">
                            {{ $lastResult['registration']->name }} &middot; {{ $lastResult['registration']->event->name }}
                            &middot; {{ $lastResult['registration']->checked_in_at->translatedFormat('H:i:s') }}
                        </p>
                    @elseif ($lastResult['type'] === 'already')
                        <p class="text-sm font-medium text-amber-800">Sudah check-in sebelumnya</p>
                        <p class="mt-1 text-sm text-amber-700">
                            {{ $lastResult['registration']->name }} sudah check-in pada
                            {{ $lastResult['registration']->checked_in_at->translatedFormat('d M Y, H:i:s') }}.
                        </p>
                    @else
                        <p class="text-sm font-medium text-red-800">Kode tidak ditemukan</p>
                        <p class="mt-1 text-sm text-red-700">
                            Kode "{{ $lastResult['code'] }}" tidak cocok dengan pendaftaran mana pun.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (function () {
            const statusEl = document.getElementById('qr-reader-status');
            let lastCode = null;
            let lastAt = 0;

            function handleDecoded(decodedText) {
                const now = Date.now();
                if (decodedText === lastCode && (now - lastAt) < 3000) {
                    return;
                }
                lastCode = decodedText;
                lastAt = now;
                $wire.checkin(decodedText);
            }

            if (typeof Html5Qrcode === 'undefined') {
                statusEl.textContent = 'Pustaka pemindai gagal dimuat. Gunakan input manual di bawah.';
                return;
            }

            Html5Qrcode.getCameras().then((devices) => {
                if (!devices || devices.length === 0) {
                    statusEl.textContent = 'Tidak ada kamera yang terdeteksi. Gunakan input manual di bawah.';
                    return;
                }

                const html5QrCode = new Html5Qrcode('qr-reader');
                html5QrCode
                    .start(
                        devices[0].id,
                        { fps: 10, qrbox: 220 },
                        handleDecoded
                    )
                    .then(() => {
                        statusEl.textContent = 'Kamera aktif. Arahkan QR ke kotak di atas.';
                    })
                    .catch(() => {
                        statusEl.textContent = 'Kamera tidak dapat diaktifkan (mungkin belum diizinkan). Gunakan input manual di bawah.';
                    });
            }).catch(() => {
                statusEl.textContent = 'Kamera tidak tersedia di perangkat ini. Gunakan input manual di bawah.';
            });
        })();
    </script>
    @endscript
</div>
