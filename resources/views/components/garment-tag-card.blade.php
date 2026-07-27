@props([
    'item' => null,       // Model Item
    'package' => null,    // Model Package (opsional, jika ini tipe paket)
    'index' => 0,         // Untuk rotasi random fisik tag
    'availability' => [], // Array 14 hari ketersediaan
    'eventDate' => null,  // Tanggal pencarian beranda
])

@php
    $isPackage = !is_null($package);
    $displayModel = $isPackage ? $package : $item;

    if (!$displayModel) {
        return;
    }

    $id = $displayModel->id;
    $name = $displayModel->name;
    $callCode = $isPackage ? 'PK-' . substr($displayModel->id, 0, 4) : $displayModel->call_code;
    $categoryName = $displayModel->category->name ?? 'Koleksi';
    $categorySlug = $displayModel->category->slug ?? '';
    
    // Warna pita kategori tetap sesuai 01-DESIGN.md §4
    $ribbonColor = match($categorySlug) {
        'gaun-klasik' => 'bg-sky-ribbon text-white',
        'gaun-modern' => 'bg-blossom-pink text-plum-ink',
        'jas-pria' => 'bg-meadow-green text-plum-ink',
        'aksesoris' => 'bg-marigold text-plum-ink',
        'paket-lengkap' => 'bg-poppy-red text-white',
        default => 'bg-plum-ink text-white',
    };

    $price = number_format($displayModel->base_price, 0, ',', '.');
    $image = $displayModel->cover_image_path 
        ? asset($displayModel->cover_image_path) 
        : 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?auto=format&fit=crop&q=80&w=600'; // Fallback aesthetic dress image

    // Rotasi default tag gantung butik agar bervariasi secara organik
    $rotations = ['-rotate-2', 'rotate-1', '-rotate-1', 'rotate-2'];
    $baseRotation = $rotations[$index % 4];
@endphp

<div 
    x-data="{ active: false }"
    @click="active = !active"
    @mouseenter="active = true"
    @mouseleave="active = false"
    class="relative w-full transition-transform duration-300 cursor-pointer"
>
    <!-- Tali Gantung (Aesthetic wireframe button effect) -->
    <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center pointer-events-none">
        <div class="w-[1.5px] h-6 bg-plum-ink/30"></div>
        <div class="w-2.5 h-2.5 rounded-full bg-petal-cream border border-plum-ink/40 shadow-inner -mt-1"></div>
    </div>

    <!-- Garment Swing Tag Card -->
    <div 
        :class="active ? 'motion-safe:animate-swing rotate-0 scale-[1.01]' : '{{ $baseRotation }}'"
        class="relative flex flex-col bg-white border-2 border-plum-ink rounded-xl p-4 transition-all duration-300 shadow-[4px_4px_0px_rgba(44,30,51,0.15)] hover:shadow-[6px_6px_0px_rgba(44,30,51,0.25)] bg-gradient-to-br from-white to-petal-cream/30"
    >
        <!-- Top Info Header -->
        <div class="flex items-center justify-between mb-3">
            <span class="px-2 py-0.5 rounded font-mono text-[9px] font-bold uppercase tracking-wider {{ $ribbonColor }}">
                {{ $categoryName }}
            </span>
            <span class="font-mono text-xs font-bold tracking-tight text-plum-ink/75">
                {{ $callCode }}
            </span>
        </div>

        <!-- Garment Image Section -->
        <div class="relative w-full aspect-[3/4] rounded-lg overflow-hidden border border-plum-ink/10 mb-3 bg-petal-cream">
            <img 
                src="{{ $image }}" 
                alt="{{ $name }}" 
                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                loading="lazy"
            >
            
            <!-- Stempel Status (01-DESIGN.md §5: stamp ink look rotated) -->
            <div class="absolute bottom-2 right-2 z-10">
                <x-stamp-badge :status="$isPackage ? 'Tersedia' : $displayModel->status" />
            </div>
        </div>

        <!-- Garment Details -->
        <div class="flex-grow">
            <h3 class="font-display text-2xl font-bold tracking-tight text-plum-ink leading-tight mb-1">
                {{ $name }}
            </h3>
            
            @if(!$isPackage && $displayModel->size_label)
                <p class="font-mono text-[10px] uppercase text-plum-ink/60 mb-2">
                    Size: {{ $displayModel->size_label }}
                </p>
            @endif

            <div class="flex items-baseline gap-1 mt-2">
                <span class="font-mono text-[10px] text-plum-ink/50 uppercase">Rent</span>
                <span class="font-mono text-sm font-bold text-plum-ink">Rp{{ $price }}</span>
                <span class="font-mono text-[9px] text-plum-ink/40">/3 hari</span>
            </div>
        </div>

        <!-- Interactive Availability Section (opens on hover) -->
        <div 
            x-show="active"
            x-collapse
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-60"
            class="mt-3 border-t border-plum-ink/10 pt-3"
            style="display: none;"
        >
            @if(!empty($availability))
                <x-date-availability-strip :availability="$availability" />

                <!-- CTA to Detail Page -->
                <a 
                    href="{{ $isPackage ? route('packages.show', $id) : route('items.show', $id) }}{{ $eventDate ? '?event_date=' . $eventDate : '' }}"
                    class="mt-3 block w-full text-center font-mono text-[10px] font-bold uppercase tracking-wider bg-marigold hover:bg-marigold/90 text-plum-ink py-2 px-3 rounded-lg border border-plum-ink transition-colors duration-200"
                >
                    {{ $eventDate ? 'Kunci Tanggal & Booking' : 'Cek Detail Koleksi' }}
                </a>
            @else
                <a 
                    href="{{ $isPackage ? route('packages.show', $id) : route('items.show', $id) }}"
                    class="block w-full text-center font-mono text-[10px] font-bold uppercase tracking-wider bg-sky-ribbon hover:bg-sky-ribbon/90 text-white py-2 px-3 rounded-lg border border-plum-ink transition-colors duration-200"
                >
                    Lihat Selengkapnya
                </a>
            @endif
        </div>
    </div>
</div>
