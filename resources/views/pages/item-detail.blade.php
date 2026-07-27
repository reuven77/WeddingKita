<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $item->name }} — WeddingKita</title>

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
                
                <!-- Left: Photo section styled like a premium physical hang tag -->
                <div class="md:col-span-5 relative">
                    <!-- Hang Tag Aesthetic Hole + String -->
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 flex flex-col items-center z-10 pointer-events-none">
                        <div class="w-[1.5px] h-8 bg-plum-ink/40"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-petal-cream border border-plum-ink/50 shadow-inner -mt-1"></div>
                    </div>

                    <div class="bg-white border-2 border-plum-ink rounded-2xl overflow-hidden shadow-[6px_6px_0px_rgba(44,30,51,1)] aspect-[3/4]">
                        <img 
                            src="{{ $item->cover_image_path ? asset($item->cover_image_path) : 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?auto=format&fit=crop&q=80&w=600' }}" 
                            alt="{{ $item->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <!-- Right: Information & Action details -->
                <div class="md:col-span-7 space-y-6">
                    <div>
                        <!-- Eyebrow collection code -->
                        <span class="font-mono text-xs font-bold tracking-widest text-plum-ink/65 uppercase block mb-1">
                            {{ $item->call_code }} · {{ $item->category->name }}
                        </span>
                        
                        <!-- Big Display Title -->
                        <h1 class="font-display text-4xl md:text-5xl font-bold tracking-tight text-plum-ink leading-tight italic">
                            {{ $item->name }}
                        </h1>
                    </div>

                    <!-- Stempel status availability -->
                    <div class="flex items-center gap-3">
                        <x-stamp-badge :status="$item->status" />
                        @if($eventDate)
                            <span class="font-mono text-[10px] bg-meadow-green/10 border border-meadow-green/30 text-meadow-green px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                                Ready for {{ \Carbon\Carbon::parse($eventDate)->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>

                    <!-- Price & Renting details -->
                    <div class="p-4 bg-white border-2 border-plum-ink rounded-xl shadow-[3px_3px_0px_rgba(44,30,51,1)] inline-block">
                        <span class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/50 block mb-1">Harga Sewa</span>
                        <div class="flex items-baseline gap-1">
                            <span class="font-mono text-xl font-bold">Rp{{ number_format($item->base_price, 0, ',', '.') }}</span>
                            <span class="font-mono text-xs text-plum-ink/60">/3 hari</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="prose max-w-none">
                        <h3 class="font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-2">Deskripsi & Detail</h3>
                        <p class="font-sans text-sm leading-relaxed text-plum-ink/85">
                            {{ $item->description }}
                        </p>
                        
                        @if($item->size_label)
                            <div class="mt-4 p-3 bg-petal-cream/60 border border-plum-ink/10 rounded-lg inline-flex items-center gap-2">
                                <span class="font-mono text-[10px] uppercase font-bold text-plum-ink/50">Ukuran Koleksi:</span>
                                <span class="font-mono text-xs font-bold text-plum-ink">{{ $item->size_label }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- 14 Days Availability Timeline -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        <x-date-availability-strip :availability="$availability" />
                    </div>

                    <!-- Booking/Fitting form -->
                    <div class="border-t border-plum-ink/10 pt-6">
                        @guest
                            <div class="mb-6 p-5 bg-marigold/15 border-2 border-marigold rounded-2xl">
                                <h4 class="font-display text-lg font-bold text-plum-ink italic mb-1">Pemesanan Memerlukan Akun</h4>
                                <p class="font-sans text-xs text-plum-ink/75 mb-4">Silakan masuk atau mendaftar terlebih dahulu untuk melakukan booking sewa dan memilih jadwal fitting di butik.</p>
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
                            <input type="hidden" name="item_id" value="{{ $item->id }}">

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
                                    :itemId="$item->id" 
                                />
                            </div>

                            <!-- Section 3: Add-on Services (MUA, dll) -->
                            <div class="space-y-3">
                                <label class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/70">
                                    3. Layanan Tambahan (Opsional)
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(\App\Models\Service::where('is_active', true)->get() as $srv)
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
                                        Jadwalkan Fitting & Booking
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
