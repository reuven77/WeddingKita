<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="WeddingKita — Rental busana pengantin premium. Gaun, jas, dan paket lengkap untuk hari istimewa Anda. Cek ketersediaan dan jadwalkan fitting eksklusif.">

        <title>WeddingKita — Rental Busana Pengantin Premium</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ─── 3D Hero Foundation ─── */
            :root {
                --plum: #2C1E33;
                --marigold: #FFB627;
                --cream: #FFF7EA;
            }

            html { scroll-behavior: smooth; }

            /* ─── Hero ─── */
            .hero-scene {
                perspective: 1200px;
                perspective-origin: 50% 40%;
                overflow: hidden;
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                background: linear-gradient(160deg, #FFF7EA 0%, #FEF3E2 40%, #F5EDE0 100%);
            }

            .hero-scene::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(ellipse 80% 60% at 70% 30%, rgba(255,182,39,0.12) 0%, transparent 60%),
                    radial-gradient(ellipse 50% 50% at 20% 80%, rgba(255,111,145,0.08) 0%, transparent 60%),
                    radial-gradient(ellipse 60% 40% at 90% 70%, rgba(47,143,224,0.06) 0%, transparent 50%);
                pointer-events: none;
            }

            .hero-scene::after {
                content: '';
                position: absolute;
                inset: 0;
                background-image: linear-gradient(rgba(44,30,51,0.03) 1px, transparent 1px),
                                  linear-gradient(90deg, rgba(44,30,51,0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                pointer-events: none;
            }

            /* ─── Floating 3D Orbs ─── */
            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(1px);
                will-change: transform;
                pointer-events: none;
            }
            .orb-1 {
                width: 320px; height: 320px;
                background: radial-gradient(circle at 35% 35%, rgba(255,182,39,0.35), rgba(255,182,39,0.05) 70%);
                top: -80px; right: 5%;
                animation: floatOrb1 8s ease-in-out infinite;
            }
            .orb-2 {
                width: 200px; height: 200px;
                background: radial-gradient(circle at 40% 40%, rgba(255,111,145,0.3), rgba(255,111,145,0.03) 70%);
                bottom: 10%; left: 3%;
                animation: floatOrb2 10s ease-in-out infinite 2s;
            }
            .orb-3 {
                width: 150px; height: 150px;
                background: radial-gradient(circle at 40% 40%, rgba(47,143,224,0.25), transparent 70%);
                top: 30%; right: 20%;
                animation: floatOrb2 7s ease-in-out infinite 1s;
            }

            @keyframes floatOrb1 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(-20px, 25px) scale(1.05); }
                66% { transform: translate(15px, -15px) scale(0.97); }
            }
            @keyframes floatOrb2 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(20px, -30px) scale(1.08); }
            }

            /* ─── 3D Floating Label Cards ─── */
            .float-card {
                position: absolute;
                background: rgba(255,255,255,0.9);
                border: 2px solid rgba(44,30,51,0.12);
                border-radius: 16px;
                padding: 12px 18px;
                backdrop-filter: blur(10px);
                box-shadow: 0 20px 60px rgba(44,30,51,0.12), 0 4px 16px rgba(44,30,51,0.08);
                will-change: transform;
                pointer-events: none;
            }
            .float-card-1 {
                top: 18%; right: 8%;
                animation: floatCard1 6s ease-in-out infinite;
                transform: rotate(3deg);
            }
            .float-card-2 {
                bottom: 25%; right: 12%;
                animation: floatCard2 8s ease-in-out infinite 1.5s;
                transform: rotate(-2deg);
            }
            .float-card-3 {
                top: 55%; left: 5%;
                animation: floatCard1 9s ease-in-out infinite 3s;
                transform: rotate(1.5deg);
            }

            @keyframes floatCard1 {
                0%, 100% { transform: rotate(3deg) translateY(0px); }
                50% { transform: rotate(3deg) translateY(-14px); }
            }
            @keyframes floatCard2 {
                0%, 100% { transform: rotate(-2deg) translateY(0px); }
                50% { transform: rotate(-2deg) translateY(-18px); }
            }

            /* ─── Hero Content ─── */
            .hero-content {
                position: relative;
                z-index: 10;
                transform-style: preserve-3d;
                transition: transform 0.1s ease-out;
            }

            /* ─── Parallax Layers ─── */
            .parallax-deep { will-change: transform; }
            .parallax-mid  { will-change: transform; }
            .parallax-top  { will-change: transform; }

            /* ─── Scroll Reveal ─── */
            .reveal {
                opacity: 0;
                transform: translateY(50px) rotateX(8deg);
                transform-origin: top center;
                transition: opacity 0.8s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal.revealed { opacity: 1; transform: translateY(0) rotateX(0deg); }
            .reveal-delay-1 { transition-delay: 0.1s; }
            .reveal-delay-2 { transition-delay: 0.2s; }
            .reveal-delay-3 { transition-delay: 0.35s; }
            .reveal-delay-4 { transition-delay: 0.5s; }

            /* ─── Search Form 3D ─── */
            .search-form-3d {
                transform-style: preserve-3d;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .search-form-3d:hover {
                transform: translateY(-4px) rotateX(1.5deg);
                box-shadow: 0 24px 60px rgba(44,30,51,0.18), 8px 8px 0px rgba(44,30,51,1);
            }

            /* ─── Stats Section ─── */
            .stats-section {
                background: var(--plum);
                position: relative;
                overflow: hidden;
            }
            .stats-section::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(ellipse 60% 80% at 10% 50%, rgba(255,182,39,0.08) 0%, transparent 60%),
                    radial-gradient(ellipse 50% 60% at 90% 50%, rgba(255,111,145,0.06) 0%, transparent 60%);
                pointer-events: none;
            }
            .stats-section::after {
                content: '';
                position: absolute;
                inset: 0;
                background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                                  linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
                background-size: 40px 40px;
                pointer-events: none;
            }

            /* ─── Scroll Progress Bar ─── */
            #scroll-progress {
                position: fixed;
                top: 0; left: 0;
                height: 3px;
                background: linear-gradient(90deg, #FFB627, #FF6F91);
                z-index: 9999;
                width: 0%;
                transition: width 0.1s linear;
                box-shadow: 0 0 8px rgba(255,182,39,0.6);
            }

            /* ─── Cursor Glow ─── */
            #cursor-glow {
                position: fixed;
                width: 300px; height: 300px;
                background: radial-gradient(circle, rgba(255,182,39,0.07) 0%, transparent 70%);
                border-radius: 50%;
                pointer-events: none;
                z-index: 0;
                transform: translate(-50%, -50%);
                transition: opacity 0.3s ease;
            }

            /* ─── Navbar Glass ─── */
            .navbar-glass {
                background: rgba(255,247,234,0.85);
                backdrop-filter: blur(16px) saturate(180%);
                -webkit-backdrop-filter: blur(16px) saturate(180%);
                border-bottom: 1.5px solid rgba(44,30,51,0.15);
                box-shadow: 0 4px 30px rgba(44,30,51,0.06);
            }

            /* ─── Hero Text Gradient ─── */
            .hero-gradient-text {
                background: linear-gradient(135deg, #2C1E33 0%, #4A2E60 60%, #2C1E33 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            @media (prefers-reduced-motion: reduce) {
                .orb, .float-card { animation: none; }
                .reveal { opacity: 1; transform: none; }
                #cursor-glow { display: none; }
                .search-form-3d:hover { transform: none; }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-plum-ink bg-petal-cream min-h-screen selection:bg-marigold selection:text-plum-ink overflow-x-hidden">

        <!-- Scroll Progress Bar -->
        <div id="scroll-progress"></div>

        <!-- Cursor Glow -->
        <div id="cursor-glow"></div>

        <!-- ─── Navigation ─── -->
        <nav x-data="{ mobileMenuOpen: false }" id="main-nav" class="sticky top-0 z-50 navbar-glass py-4 px-6 md:px-12 transition-all duration-300">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="font-display text-3xl font-bold tracking-tight italic text-plum-ink hover:opacity-85 transition-opacity">
                    WeddingKita
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8 font-mono text-xs uppercase tracking-widest font-bold">
                    <a href="/" class="relative py-1 border-b-2 border-marigold text-plum-ink">
                        Koleksi
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-block font-mono text-[10px] uppercase font-bold tracking-widest px-4 py-2 border-2 border-plum-ink rounded-lg bg-marigold text-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_rgba(44,30,51,1)] transition-all">
                            Dashboard
                        </a>
                    @else
                        <div x-data="{ dropdownOpen: false }" class="relative hidden sm:block">
                            <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false" type="button" class="font-mono text-[10px] uppercase font-bold tracking-widest px-4 py-2 border-2 border-plum-ink rounded-lg bg-marigold text-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_rgba(44,30,51,1)] transition-all flex items-center gap-2">
                                Klik untuk Melanjutkan
                                <svg class="w-3.5 h-3.5 transition-transform" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="dropdownOpen" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white border-2 border-plum-ink rounded-xl shadow-[4px_4px_0px_rgba(44,30,51,1)] py-2 z-50">
                                <a href="{{ route('login') }}" class="block px-4 py-2.5 font-mono text-xs font-bold uppercase tracking-wider text-plum-ink hover:bg-petal-cream transition-colors border-b border-plum-ink/10">
                                    Masuk (Login)
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2.5 font-mono text-xs font-bold uppercase tracking-wider text-plum-ink hover:bg-marigold/30 transition-colors">
                                    Daftar (Register)
                                </a>
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 rounded-lg border-2 border-plum-ink bg-white text-plum-ink focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Drawer -->
            <div x-show="mobileMenuOpen" x-collapse x-cloak class="md:hidden pt-4 pb-2 border-t-2 border-plum-ink/10 mt-4 space-y-3 font-mono text-xs uppercase font-bold tracking-widest">
                <a href="/" class="block py-2 px-3 bg-marigold/20 rounded-lg text-plum-ink">Koleksi</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block py-2 px-3 text-plum-ink/80 hover:bg-plum-ink/5 rounded-lg">Dashboard Saya</a>
                    <a href="{{ route('profile.edit') }}" class="block py-2 px-3 text-plum-ink/80 hover:bg-plum-ink/5 rounded-lg">Profil Saya</a>
                @else
                    <div class="pt-2 border-t border-plum-ink/10 space-y-2">
                        <div class="font-mono text-[10px] text-plum-ink/50 uppercase font-bold px-1">Klik untuk Melanjutkan</div>
                        <div class="flex gap-2">
                            <a href="{{ route('login') }}" class="flex-1 text-center py-2 bg-white border-2 border-plum-ink rounded-lg text-plum-ink">Masuk (Login)</a>
                            <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-marigold border-2 border-plum-ink rounded-lg text-plum-ink">Daftar (Register)</a>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>

        <!-- ─── HERO SECTION (3D) ─── -->
        <section class="hero-scene" id="hero">
            <!-- Floating Orbs -->
            <div class="orb orb-1 parallax-deep"></div>
            <div class="orb orb-2 parallax-mid"></div>
            <div class="orb orb-3 parallax-top"></div>

            <!-- Floating Info Cards -->
            <div class="float-card float-card-1 hidden lg:block">
                <div class="font-mono text-[9px] text-plum-ink/50 uppercase tracking-widest mb-1">Status</div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="font-mono text-xs font-bold text-plum-ink">100+ Koleksi Tersedia</span>
                </div>
            </div>

            <div class="float-card float-card-2 hidden lg:block">
                <div class="font-mono text-[9px] text-plum-ink/50 uppercase tracking-widest mb-1.5">Rating Butik</div>
                <div class="flex items-center gap-1.5">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-3.5 h-3.5 fill-marigold" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="font-mono text-xs font-bold text-plum-ink ml-1">5.0</span>
                </div>
            </div>

            <div class="float-card float-card-3 hidden lg:block">
                <div class="font-mono text-[9px] text-plum-ink/50 uppercase tracking-widest mb-1">Fitting Gratis</div>
                <div class="font-mono text-xs font-bold text-plum-ink">Setiap Pemesanan</div>
            </div>

            <!-- Hero Content -->
            <div class="hero-content w-full max-w-7xl mx-auto px-6 md:px-12 py-20 md:py-28 lg:py-36" id="hero-inner">
                <div class="max-w-3xl">
                    <!-- Eyebrow -->
                    <div class="reveal mb-6 inline-flex items-center gap-3">
                        <span class="h-px w-8 bg-plum-ink/40"></span>
                        <span class="font-mono text-[10px] font-bold tracking-[0.3em] uppercase text-plum-ink/65">WK · Butik Busana Pengantin Premium</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="reveal reveal-delay-1 font-display text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight italic leading-[1.05] mb-6">
                        <span class="hero-gradient-text">Busana impian</span><br>
                        <span class="text-plum-ink">untuk hari yang</span><br>
                        <span class="relative inline-block">
                            <span class="text-plum-ink">tak terlupakan.</span>
                            <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 12" fill="none" preserveAspectRatio="none">
                                <path d="M0 8 Q75 2 150 8 Q225 14 300 8" stroke="#FFB627" stroke-width="3" fill="none" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </h1>

                    <!-- Subheading -->
                    <p class="reveal reveal-delay-2 font-sans text-base md:text-lg text-plum-ink/70 max-w-xl mb-10 leading-relaxed">
                        Pilih tanggal pernikahan Anda, telusuri koleksi gaun dan jas yang tersedia, lalu kunci jadwal fitting eksklusif Anda di butik kami.
                    </p>

                    <!-- Search Form -->
                    <div class="reveal reveal-delay-3">
                        <form action="{{ route('home') }}" method="GET" class="search-form-3d bg-white border-2 border-plum-ink p-4 rounded-2xl shadow-[6px_6px_0px_rgba(44,30,51,1)] flex flex-col md:flex-row gap-4 items-stretch max-w-2xl">
                            <div class="flex-grow flex flex-col items-start px-2 py-1 md:border-r border-plum-ink/10">
                                <label for="search" class="font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Cari Busana</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Gaun satin, Tuxedo, Tiara..."
                                    class="w-full bg-transparent border-0 p-0 text-sm font-sans focus:ring-0 placeholder:text-plum-ink/30 text-plum-ink font-semibold">
                            </div>
                            <div class="flex-grow flex flex-col items-start px-2 py-1">
                                <label for="event_date" class="font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Tanggal Acara Pernikahan</label>
                                <input type="date" name="event_date" id="event_date" min="{{ date('Y-m-d') }}" value="{{ request('event_date') }}"
                                    class="w-full bg-transparent border-0 p-0 text-sm font-mono focus:ring-0 text-plum-ink font-semibold">
                            </div>
                            <button type="submit" class="bg-marigold text-plum-ink font-mono text-xs font-bold uppercase tracking-widest px-8 py-4 rounded-xl border-2 border-plum-ink hover:bg-marigold/95 transition-all shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_rgba(44,30,51,1)] shrink-0">
                                Cek Ketersediaan
                            </button>
                        </form>
                    </div>

                    <!-- Trust Signals -->
                    <div class="reveal reveal-delay-4 flex items-center gap-6 mt-6 font-mono text-[10px] text-plum-ink/50 uppercase tracking-wider">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3 fill-meadow-green" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Fitting Gratis
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3 fill-meadow-green" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tanpa Deposit
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3 fill-meadow-green" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Koleksi Premium
                        </span>
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2">
                <span class="font-mono text-[9px] uppercase tracking-widest text-plum-ink/40">Scroll</span>
                <div class="w-5 h-8 border-2 border-plum-ink/25 rounded-full flex items-start justify-center pt-1.5">
                    <div class="w-1 h-2 bg-plum-ink/40 rounded-full animate-bounce"></div>
                </div>
            </div>
        </section>

        <!-- ─── STATS SECTION ─── -->
        <section class="stats-section py-16 md:py-24">
            <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
                    @php
                        $stats = [
                            ['num' => '100+', 'label' => 'Koleksi Busana',   'sub' => 'Gaun, Jas & Aksesoris'],
                            ['num' => '500+', 'label' => 'Pasangan Bahagia', 'sub' => 'Telah Mempercayai Kami'],
                            ['num' => '5',    'label' => 'Ruang Fitting',    'sub' => 'Tersedia Setiap Hari'],
                            ['num' => '3 Hr', 'label' => 'Durasi Sewa',      'sub' => 'H-2 Hingga H+1 Acara'],
                        ];
                    @endphp
                    @foreach($stats as $i => $stat)
                        <div class="reveal text-center" style="transition-delay: {{ $i * 0.1 }}s">
                            <div class="font-display text-4xl md:text-5xl font-bold italic text-marigold mb-1">{{ $stat['num'] }}</div>
                            <div class="font-mono text-xs font-bold uppercase tracking-widest text-white mb-1">{{ $stat['label'] }}</div>
                            <div class="font-sans text-[11px] text-white/40">{{ $stat['sub'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ─── Kategori Ribbon Section ─── -->
        <section class="max-w-7xl mx-auto px-6 md:px-12 py-10 mb-4">
            <h2 class="font-mono text-xs font-bold tracking-widest uppercase text-plum-ink/55 mb-6 text-center md:text-left">
                Pita Kategori Butik
            </h2>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                <!-- Semua Kategori -->
                <a href="?{{ http_build_query(request()->only(['event_date', 'search'])) }}"
                   class="px-4 py-2 border-2 border-plum-ink rounded-lg font-mono text-[10px] font-bold uppercase tracking-wider transition-all {{ empty($selectedCategory) ? 'bg-plum-ink text-white shadow-[2px_2px_0px_rgba(44,30,51,1)]' : 'bg-white hover:bg-petal-cream text-plum-ink' }}">
                    Semua Koleksi
                </a>

                @foreach($categories as $cat)
                    @php
                        // Warna solid per kategori — bg + text + hover semuanya self-contained
                        // Tidak boleh ada 'bg-white' yang berkonflik di kondisi unselected
                        $ribbonClasses = match($cat->slug) {
                            'gaun-klasik'   => 'bg-sky-ribbon text-white border-sky-ribbon hover:opacity-90',
                            'gaun-modern'   => 'bg-blossom-pink text-plum-ink border-blossom-pink hover:opacity-90',
                            'jas-pria'      => 'bg-meadow-green text-plum-ink border-meadow-green hover:opacity-90',
                            'aksesoris'     => 'bg-marigold text-plum-ink border-marigold hover:opacity-90',
                            'paket-lengkap' => 'bg-poppy-red text-white border-poppy-red hover:opacity-90',
                            default         => 'bg-plum-ink text-white border-plum-ink hover:opacity-90',
                        };
                        $isSelected = ($selectedCategory === $cat->slug);
                    @endphp
                    <a href="?category={{ $cat->slug }}{{ request('event_date') ? '&event_date=' . request('event_date') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}"
                       class="px-4 py-2 border-2 rounded-lg font-mono text-[10px] font-bold uppercase tracking-wider transition-all {{ $isSelected ? 'bg-plum-ink text-white border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)]' : $ribbonClasses }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </section>

        <!-- ─── Main Catalog Section ─── -->
        <main class="max-w-7xl mx-auto px-6 md:px-12 pb-24">

            @if(request('event_date'))
                <div class="mb-8 p-4 bg-meadow-green/10 border-2 border-meadow-green/40 rounded-xl flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-meadow-green animate-pulse"></span>
                    <p class="font-sans text-sm text-plum-ink font-semibold">
                        Menampilkan hasil pencarian yang TERSEDIA untuk tanggal acara:
                        <span class="font-mono bg-white border border-plum-ink/20 px-2 py-0.5 rounded text-xs">
                            {{ \Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y') }}
                        </span>
                    </p>
                </div>
            @endif

            <!-- 1. Section Paket Lengkap -->
            @if($packages->isNotEmpty())
                <div class="mb-16">
                    <div class="flex items-baseline justify-between mb-8 border-b-2 border-plum-ink pb-2">
                        <h2 class="font-display text-3xl font-bold tracking-tight text-plum-ink italic">
                            Paket Lengkap Butik
                        </h2>
                        <span class="font-mono text-[10px] uppercase font-bold text-plum-ink/50">
                            Bundling Busana + Jasa
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
                        @foreach($packages as $pkg)
                            <x-garment-tag-card :package="$pkg" :index="$loop->index" :eventDate="$eventDate" />
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 2. Section Individual Items -->
            <div>
                <div class="flex items-baseline justify-between mb-8 border-b-2 border-plum-ink pb-2">
                    <h2 class="font-display text-3xl font-bold tracking-tight text-plum-ink italic">
                        Koleksi Busana Tunggal
                    </h2>
                    <span class="font-mono text-[10px] uppercase font-bold text-plum-ink/50">
                        Gaun, Jas &amp; Aksesoris
                    </span>
                </div>

                @if($items->isEmpty())
                    <div class="text-center py-16 bg-white border-2 border-plum-ink border-dashed rounded-2xl">
                        <p class="font-display text-2xl italic text-plum-ink/60 mb-2">Tidak ada busana yang ditemukan</p>
                        <p class="font-sans text-xs text-plum-ink/40">Coba ubah filter pencarian atau tanggal acara Anda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12 mb-12">
                        @foreach($items as $item)
                            <x-garment-tag-card :item="$item" :index="$loop->index" :availability="$availabilities[$item->id] ?? []" :eventDate="$eventDate" />
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="mt-12 font-mono text-xs border-t border-plum-ink/10 pt-8">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </main>

        <!-- ─── Footer ─── -->
        <footer class="border-t-2 border-plum-ink bg-white/50 py-12 px-6 md:px-12 text-center text-plum-ink/50 font-mono text-[10px] uppercase tracking-widest">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <span>© {{ date('Y') }} WeddingKita. All rights reserved.</span>
                <span class="flex items-center gap-1 font-bold text-plum-ink/75">
                    Premium Bridal Gown &amp; Suit Rental
                </span>
            </div>
        </footer>

        <!-- ─── 3D Interaction Scripts ─── -->
        <script>
        (function() {
            'use strict';

            // ── Scroll Progress Bar ──
            const progressBar = document.getElementById('scroll-progress');
            function updateProgress() {
                const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                progressBar.style.width = Math.min(100, pct) + '%';
            }
            window.addEventListener('scroll', updateProgress, { passive: true });

            // ── Cursor Glow ──
            const cursorGlow = document.getElementById('cursor-glow');
            let cursorX = 0, cursorY = 0, glowX = 0, glowY = 0;
            document.addEventListener('mousemove', e => { cursorX = e.clientX; cursorY = e.clientY; });
            function animateCursor() {
                glowX += (cursorX - glowX) * 0.08;
                glowY += (cursorY - glowY) * 0.08;
                cursorGlow.style.left = glowX + 'px';
                cursorGlow.style.top  = glowY + 'px';
                requestAnimationFrame(animateCursor);
            }
            animateCursor();

            // ── Hero 3D Mouse Parallax ──
            const heroSection = document.getElementById('hero');
            const heroInner   = document.getElementById('hero-inner');
            const parallaxDeep = document.querySelectorAll('.parallax-deep');
            const parallaxMid  = document.querySelectorAll('.parallax-mid');
            const parallaxTop  = document.querySelectorAll('.parallax-top');

            let mouseX = 0, mouseY = 0, targetX = 0, targetY = 0;

            if (heroSection) {
                heroSection.addEventListener('mousemove', e => {
                    const rect = heroSection.getBoundingClientRect();
                    mouseX = ((e.clientX - rect.left) / rect.width  - 0.5) * 2;
                    mouseY = ((e.clientY - rect.top)  / rect.height - 0.5) * 2;
                });
                heroSection.addEventListener('mouseleave', () => { mouseX = 0; mouseY = 0; });
            }

            function animateParallax() {
                targetX += (mouseX - targetX) * 0.06;
                targetY += (mouseY - targetY) * 0.06;
                if (heroInner) {
                    heroInner.style.transform = `rotateY(${targetX * 3}deg) rotateX(${-targetY * 2}deg) translateZ(0)`;
                }
                parallaxDeep.forEach(el => { el.style.transform = `translate(${targetX * -30}px, ${targetY * -20}px)`; });
                parallaxMid.forEach(el  => { el.style.transform = `translate(${targetX * -18}px, ${targetY * -12}px)`; });
                parallaxTop.forEach(el  => { el.style.transform = `translate(${targetX * -8}px, ${targetY * -5}px)`;  });
                requestAnimationFrame(animateParallax);
            }
            animateParallax();

            // ── Intersection Observer for Scroll Reveal ──
            const revealEls = document.querySelectorAll('.reveal');
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            revealEls.forEach(el => revealObserver.observe(el));

        })();
        </script>

    </body>
</html>
