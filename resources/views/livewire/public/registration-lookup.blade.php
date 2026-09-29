<div>
    <h1 class="text-2xl font-semibold text-slate-900">Cek status pendaftaran</h1>
    <p class="mt-1 text-sm text-slate-600">Masukkan email dan kode pendaftaran yang Anda terima saat mendaftar untuk melihat kembali QR dan status check-in Anda.</p>

    <form wire:submit="search" class="mt-6 space-y-4 rounded-lg border border-slate-200 bg-white p-6">
        <div>
            <label for="lookup-email" class="block text-sm font-medium text-slate-700">Email</label>
            <input
                type="email"
                id="lookup-email"
                wire:model="email"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                autocomplete="email"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="lookup-code" class="block text-sm font-medium text-slate-700">Kode pendaftaran</label>
            <input
                type="text"
                id="lookup-code"
                wire:model="registration_code"
                class="mt-1 block w-full rounded-md border-slate-300 font-mono uppercase shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                placeholder="Contoh: A1B2C3D4"
            >
            @error('registration_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="search"
            class="inline-flex w-full items-center justify-center rounded-md bg-teal-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
        >
            <span wire:loading.remove wire:target="search">Cari pendaftaran</span>
            <span wire:loading wire:target="search">Mencari...</span>
        </button>
    </form>

    @if ($searched)
        <div class="mt-6">
            @if ($result)
                @include('livewire.public.partials.registration-details', ['registration' => $result, 'qrSvg' => $qrSvg])
            @else
                <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center">
                    <p class="text-sm text-slate-600">Tidak ada pendaftaran yang cocok dengan email dan kode tersebut. Periksa kembali ejaan email dan kode Anda.</p>
                </div>
            @endif
        </div>
    @endif
</div>
