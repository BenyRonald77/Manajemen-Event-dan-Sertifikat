<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2">
                <a href="{{ route('panitia.events.index') }}" wire:navigate class="block rounded-lg border border-slate-200 bg-white p-6 hover:border-teal-300">
                    <h3 class="text-base font-semibold text-slate-900">Kelola Acara</h3>
                    <p class="mt-1 text-sm text-slate-600">Buat acara baru, lihat daftar pendaftar, dan generate sertifikat untuk peserta yang sudah check-in.</p>
                </a>
                <a href="{{ route('panitia.checkin') }}" wire:navigate class="block rounded-lg border border-slate-200 bg-white p-6 hover:border-teal-300">
                    <h3 class="text-base font-semibold text-slate-900">Scan Check-in</h3>
                    <p class="mt-1 text-sm text-slate-600">Pindai QR peserta di lokasi acara, atau masukkan kode pendaftaran secara manual.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
