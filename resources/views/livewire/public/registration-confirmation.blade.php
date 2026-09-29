<div>
    @if (! $registration)
        <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
            <h1 class="text-lg font-semibold text-slate-900">Pendaftaran tidak ditemukan</h1>
            <p class="mt-2 text-sm text-slate-600">Kode pendaftaran pada tautan ini tidak cocok dengan data kami. Periksa kembali tautan yang Anda buka, atau gunakan pencarian status pendaftaran.</p>
            <a href="{{ route('registrations.lookup') }}" wire:navigate class="mt-4 inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">
                Cek status pendaftaran
            </a>
        </div>
    @else
        <p class="mb-3 text-sm font-medium text-emerald-700">Pendaftaran berhasil</p>

        @include('livewire.public.partials.registration-details', ['registration' => $registration, 'qrSvg' => $qrSvg])

        <p class="mt-4 text-center text-sm text-slate-500">
            Simpan tautan halaman ini, atau gunakan
            <a href="{{ route('registrations.lookup') }}" wire:navigate class="font-medium text-teal-700 hover:text-teal-800">cek status pendaftaran</a>
            untuk membukanya kembali kapan saja.
        </p>
    @endif
</div>
