<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}?v=2">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}?v=2">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-plum-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-petal-cream">
            <div>
                <a href="/" class="flex items-center gap-3 hover:opacity-90 transition">
                    <img src="{{ asset('images/logoWK.png') }}" alt="WeddingKita Logo" class="h-10 w-auto md:h-12 shrink-0 object-contain">
                    <span class="font-display text-4xl font-bold tracking-tight text-plum-ink italic whitespace-nowrap">WeddingKita</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/70 backdrop-blur border border-plum-ink/10 shadow-xl sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>

</html>
