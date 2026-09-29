<div>
    <a href="{{ route('events.public-index') }}" wire:navigate class="text-sm text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar acara</a>

    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-slate-900">Daftar: {{ $event->name }}</h1>
        <p class="mt-1 text-sm text-slate-600">
            {{ $event->starts_at->translatedFormat('d M Y, H:i') }} &mdash; {{ $event->ends_at->translatedFormat('d M Y, H:i') }}
        </p>
        @if ($event->location)
            <p class="text-sm text-slate-500">{{ $event->location }}</p>
        @endif

        @if ($quotaFull)
            <div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">Kuota event ini sudah penuh.</p>
                <p class="mt-1 text-sm text-red-700">Pendaftaran untuk acara ini sudah ditutup karena kuota {{ $event->quota }} peserta telah tercapai.</p>
            </div>
        @else
            <form wire:submit="register" class="mt-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama lengkap</label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                        autocomplete="name"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700">Nomor telepon (opsional)</label>
                    <input
                        type="text"
                        id="phone"
                        wire:model="phone"
                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                        autocomplete="tel"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="register"
                    class="inline-flex w-full items-center justify-center rounded-md bg-teal-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                >
                    <span wire:loading.remove wire:target="register">Daftar sekarang</span>
                    <span wire:loading wire:target="register">Mendaftarkan...</span>
                </button>
            </form>
        @endif
    </div>
</div>
