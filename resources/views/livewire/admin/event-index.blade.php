<div>
    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-600">Daftar acara yang sudah dibuat. Klik salah satu untuk melihat pendaftar dan mengelola sertifikat.</p>
                <button
                    type="button"
                    wire:click="$set('showCreateForm', true)"
                    class="inline-flex shrink-0 items-center rounded-md bg-teal-700 min-h-[44px] px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
                >
                    Buat acara baru
                </button>
            </div>

            @if ($showCreateForm)
                <div class="rounded-lg border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900">Acara baru</h3>
                        <button type="button" wire:click="$set('showCreateForm', false)" class="text-sm text-slate-500 hover:text-slate-700">
                            Batal
                        </button>
                    </div>

                    <form wire:submit="create" class="mt-4 space-y-4">
                        <div>
                            <label for="event-name" class="block text-sm font-medium text-slate-700">Nama acara</label>
                            <input type="text" id="event-name" wire:model="name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="event-description" class="block text-sm font-medium text-slate-700">Deskripsi (opsional)</label>
                            <textarea id="event-description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"></textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="event-location" class="block text-sm font-medium text-slate-700">Lokasi (opsional)</label>
                            <input type="text" id="event-location" wire:model="location" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="event-starts" class="block text-sm font-medium text-slate-700">Mulai</label>
                                <input type="datetime-local" id="event-starts" wire:model="starts_at" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="event-ends" class="block text-sm font-medium text-slate-700">Selesai</label>
                                <input type="datetime-local" id="event-ends" wire:model="ends_at" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="event-quota" class="block text-sm font-medium text-slate-700">Kuota peserta (opsional, kosongkan jika tanpa batas)</label>
                            <input type="number" min="1" id="event-quota" wire:model="quota" class="mt-1 block w-full max-w-[160px] rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            @error('quota') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="create"
                            class="inline-flex items-center rounded-md bg-teal-700 min-h-[44px] px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="create">Simpan acara</span>
                            <span wire:loading wire:target="create">Menyimpan...</span>
                        </button>
                    </form>
                </div>
            @endif

            @if ($events->isEmpty())
                <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-sm text-slate-600">Belum ada acara. Klik "Buat acara baru" untuk memulai.</p>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($events as $event)
                        <li>
                            <a href="{{ route('panitia.events.show', $event) }}" wire:navigate class="block rounded-lg border border-slate-200 bg-white p-5 hover:border-teal-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-base font-semibold text-slate-900">{{ $event->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $event->starts_at->translatedFormat('d M Y, H:i') }}
                                            @if ($event->location)
                                                &middot; {{ $event->location }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium text-slate-500">{{ $event->registrations_count }} pendaftar</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
