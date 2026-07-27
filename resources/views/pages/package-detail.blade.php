<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $package->name }} — WeddingKita</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/logoWK.png') }}">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-plum-ink bg-petal-cream min-h-screen selection:bg-marigold selection:text-plum-ink">
        
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 bg-petal-cream/80 backdrop-blur border-b-2 border-plum-ink py-4 px-6 md:px-12">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5 hover:opacity-90 transition">
                    <img src="{{ asset('images/logoWK.png') }}" alt="WeddingKita Logo" class="h-8 w-auto md:h-10 shrink-0 object-contain">
                    <span class="font-display text-2xl md:text-3xl font-bold italic text-plum-ink tracking-tight whitespace-nowrap">WeddingKita</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/" class="font-mono text-xs uppercase font-bold text-plum-ink/75 hover:text-plum-ink">
                        ← Kembali ke Katalog
                    </a>
                </div>
            </div>
        </nav>

        <!-- Container -->
        <main class="max-w-6xl mx-auto px-6 py-12 md:py-16">
            
            @if(session('error'))
                <div class="mb-8 p-4 bg-poppy-red/10 border-2 border-poppy-red text-poppy-red rounded-xl font-mono text-xs font-bold uppercase tracking-wider">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-8 p-4 bg-meadow-green/10 border-2 border-meadow-green text-meadow-green rounded-xl font-mono text-xs font-bold uppercase tracking-wider">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                
                <!-- Left: Package Photo with aesthetic string -->
                <div class="md:col-span-5 relative">
                    <!-- Hang Tag Aesthetic Hole + String -->
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 flex flex-col items-center z-10 pointer-events-none">
                        <div class="w-[1.5px] h-8 bg-plum-ink/40"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-petal-cream border border-plum-ink/50 shadow-inner -mt-1"></div>
                    </div>

                    <div class="bg-white border-2 border-plum-ink rounded-2xl overflow-hidden shadow-[6px_6px_0px_rgba(44,30,51,1)] aspect-[3/4]">
                        <img 
                            src="{{ $package->cover_image_path ? asset($package->cover_image_path) : 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?auto=format&fit=crop&q=80&w=600' }}" 
                            alt="{{ $package->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <!-- Right: Information, Items contained, & Fitting Form -->
                <div class="md:col-span-7 space-y-6">
                    <div>
                        <span class="font-mono text-xs font-bold tracking-widest text-plum-ink/65 uppercase block mb-1">
                            PAKET LENGKAP · {{ $package->category->name }}
                        </span>
                        
                        <h1 class="font-display text-4xl md:text-5xl font-bold tracking-tight text-plum-ink leading-tight italic">
                            {{ $package->name }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-stamp-badge status="Tersedia" />
                    </div>

                    <!-- Price panel -->
                    <div class="p-4 bg-white border-2 border-plum-ink rounded-xl shadow-[3px_3px_0px_rgba(44,30,51,1)] inline-block">
                        <span class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/50 block mb-1">Harga Paket</span>
                        <div class="flex items-baseline gap-1">
                            <span class="font-mono text-xl font-bold">Rp{{ number_format($package->base_price, 0, ',', '.') }}</span>
                            <span class="font-mono text-xs text-plum-ink/60">/3 hari</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="prose max-w-none">
                        <h3 class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-2">Deskripsi Paket</h3>
                        <p class="font-sans text-sm leading-relaxed text-plum-ink/85">
                            {{ $package->description }}
                        </p>
                    </div>

                    <!-- Items Included in Package (Pivot: package_items) -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        <h3 class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-4">
                            Isi Paket Fisik (Busana & Aksesoris)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($package->items as $item)
                                <a 
                                    href="{{ route('items.show', $item->id) }}" 
                                    class="p-3 bg-white border-2 border-plum-ink/10 hover:border-plum-ink rounded-xl transition-all flex flex-col justify-between"
                                >
                                    <div>
                                        <span class="font-mono text-[8px] text-plum-ink/50 block uppercase mb-1">{{ $item->call_code }}</span>
                                        <span class="font-sans text-xs font-bold leading-tight block text-plum-ink">{{ $item->name }}</span>
                                    </div>
                                    <span class="font-mono text-[9px] text-plum-ink/60 mt-2 block">{{ $item->size_label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Services Included in Package (Pivot: package_services) -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        <h3 class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-3">
                            Layanan & Jasa Otomatis Termasuk
                        </h3>
                        <ul class="space-y-2">
                            @foreach($package->services as $srv)
                                @if($srv->pivot->is_default)
                                    <li class="flex items-start gap-2 text-xs">
                                        <span class="text-meadow-green font-bold">✔</span>
                                        <div class="font-sans">
                                            <strong class="text-plum-ink">{{ $srv->name }}</strong>
                                            <span class="text-plum-ink/60"> — {{ $srv->description }}</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <!-- 14 Days Availability Timeline (calculated from child items availability) -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        <x-date-availability-strip :availability="$availability" />
                    </div>

                    <!-- Booking/Fitting form -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        @guest
                            <div class="mb-6 p-5 bg-marigold/15 border-2 border-marigold rounded-2xl">
                                <h4 class="font-display text-lg font-bold text-plum-ink italic mb-1">Pemesanan Memerlukan Akun</h4>
                                <p class="font-sans text-xs text-plum-ink/75 mb-4">Silakan masuk atau mendaftar terlebih dahulu untuk melakukan booking sewa paket dan memilih jadwal fitting di butik.</p>
                                <div class="flex gap-3">
                                    <a href="{{ route('login') }}" class="font-mono text-xs font-bold uppercase tracking-wider bg-plum-ink text-white py-2.5 px-4 rounded-xl border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,0.3)] hover:bg-plum-ink/90 transition">
                                        Masuk Ke Akun
                                    </a>
                                    <a href="{{ route('register') }}" class="font-mono text-xs font-bold uppercase tracking-wider bg-white text-plum-ink py-2.5 px-4 rounded-xl border-2 border-plum-ink hover:bg-petal-cream transition">
                                        Daftar Akun Baru
                                    </a>
                                </div>
                            </div>
                        @endguest

                        <form action="{{ route('rentals.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">

                            <!-- Section 1: Tanggal Acara Pernikahan -->
                            <div class="space-y-2">
                                <label for="event_date" class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/70">
                                    1. Tentukan Tanggal Acara Pernikahan
                                </label>
                                <input 
                                    type="date" 
                                    name="event_date" 
                                    id="event_date" 
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ old('event_date', $eventDate ?? request('event_date')) }}"
                                    @guest disabled @endguest
                                    required
                                    class="w-full max-w-xs bg-white border-2 border-plum-ink p-3 rounded-xl font-mono text-sm focus:ring-0 focus:border-marigold focus:shadow-[2px_2px_0px_rgba(44,30,51,1)] transition-all text-plum-ink font-semibold disabled:opacity-50"
                                >
                                @error('event_date')
                                    <p class="font-mono text-[10px] text-poppy-red font-bold uppercase tracking-wider mt-1">{{ $message }}</p>
                                @enderror
                                <p class="font-sans text-[10px] text-plum-ink/50">Pengambilan H-2 dan pengembalian H+1 dihitung otomatis.</p>
                            </div>

                            <!-- Section 2: Fitting Schedule Calendar -->
                            <div class="space-y-2">
                                <label class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/70">
                                    2. Pilih Jadwal Fitting di Butik <span class="text-poppy-red font-bold">(Wajib)</span>
                                </label>
                                @error('scheduled_date')
                                    <p class="font-mono text-[10px] text-poppy-red font-bold uppercase tracking-wider mb-1">{{ $message }}</p>
                                @enderror
                                @error('scheduled_time')
                                    <p class="font-mono text-[10px] text-poppy-red font-bold uppercase tracking-wider mb-1">{{ $message }}</p>
                                @enderror
                                <x-fitting-calendar 
                                    :availableSlots="$fittingSlots" 
                                    :selectedDate="$selectedDate" 
                                    :packageId="$package->id" 
                                />
                            </div>

                            <!-- Section 3: Add-on Services -->
                            <div class="space-y-3">
                                <label class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/70">
                                    3. Tambah Layanan Ekstra (Opsional)
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($package->services as $srv)
                                        @if(!$srv->pivot->is_default)
                                            <label class="flex items-start gap-3 p-3 bg-white border-2 border-plum-ink/10 rounded-xl cursor-pointer hover:border-plum-ink transition-all">
                                                <input 
                                                    type="checkbox" 
                                                    name="addon_services[]" 
                                                    value="{{ $srv->id }}"
                                                    @guest disabled @endguest
                                                    class="rounded border-plum-ink/20 text-marigold focus:ring-0 focus:ring-offset-0 mt-0.5"
                                                >
                                                <div class="flex-grow">
                                                    <span class="block font-sans text-xs font-bold text-plum-ink">{{ $srv->name }}</span>
                                                    <span class="block font-mono text-[9px] text-plum-ink/50 mt-0.5">+Rp{{ number_format($srv->price, 0, ',', '.') }}</span>
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit action button -->
                            <div class="pt-4">
                                @auth
                                    <button 
                                        type="submit"
                                        class="w-full md:max-w-xs block text-center font-mono text-xs font-bold uppercase tracking-wider bg-marigold hover:bg-marigold/95 text-plum-ink py-4 px-6 rounded-xl border-2 border-plum-ink shadow-[4px_4px_0px_rgba(44,30,51,1)] hover:translate-y-[-1px] hover:shadow-[5px_5px_0px_rgba(44,30,51,1)] transition-all"
                                    >
                                        Jadwalkan Fitting & Booking Paket
                                    </button>
                                @else
                                    <button 
                                        type="button"
                                        disabled
                                        class="w-full md:max-w-xs block text-center font-mono text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-500 py-4 px-6 rounded-xl border-2 border-gray-300 cursor-not-allowed"
                                    >
                                        Login untuk Booking
                                    </button>
                                @endauth
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t-2 border-plum-ink bg-white/50 py-12 px-6 md:px-12 text-center text-plum-ink/50 font-mono text-[10px] uppercase tracking-widest mt-24">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logoWK.png') }}" alt="WeddingKita Logo" class="h-8 w-auto md:h-10 object-contain">
                    <span>© {{ date('Y') }} WeddingKita. All rights reserved.</span>
                </div>
                <span class="font-bold text-plum-ink/75">Premium Bridal Gown & Suit Rental</span>
            </div>
        </footer>

    </body>
</html>
