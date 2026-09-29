<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-4 sm:px-6">
                    <a href="{{ route('home') }}" wire:navigate class="text-base font-semibold text-slate-900">
                        {{ config('app.name') }}
                    </a>
                    <nav class="flex items-center gap-4 text-sm">
                        <a href="{{ route('registrations.lookup') }}" wire:navigate class="text-slate-600 hover:text-slate-900">
                            Cek status pendaftaran
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate class="font-medium text-teal-700 hover:text-teal-800">
                                Panel panitia
                            </a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="font-medium text-teal-700 hover:text-teal-800">
                                Masuk panitia
                            </a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="flex-1">
                <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto max-w-3xl px-4 py-4 text-center text-xs text-slate-500 sm:px-6">
                    {{ config('app.name') }}
                </div>
            </footer>
        </div>
    </body>
</html>
