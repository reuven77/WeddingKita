@props([
    'availability' => [] // Key: Y-m-d, Value: boolean (true = available, false = booked)
])

<div class="p-3 bg-petal-cream/80 border border-plum-ink/10 rounded-xl shadow-inner">
    <div class="flex items-center justify-between mb-2">
        <span class="font-mono text-[10px] uppercase tracking-widest text-plum-ink/55">Jadwal 14 Hari</span>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1 font-mono text-[9px] text-plum-ink/60">
                <span class="w-1.5 h-1.5 rounded-full bg-meadow-green border border-plum-ink/20"></span> Ready
            </span>
            <span class="flex items-center gap-1 font-mono text-[9px] text-plum-ink/60">
                <span class="w-1.5 h-1.5 rounded-full bg-poppy-red border border-plum-ink/20"></span> Booked
            </span>
        </div>
    </div>
    
    <div class="grid grid-cols-7 gap-1">
        @foreach($availability as $dateStr => $isAvailable)
            @php
                $carbonDate = \Carbon\Carbon::parse($dateStr);
                $dayName = substr($carbonDate->locale('id')->dayName, 0, 1); // S, S, R, K, J, S, M
                $dayNum = $carbonDate->format('d');
            @endphp
            <div 
                x-data="{ showTooltip: false }" 
                class="relative flex flex-col items-center justify-center py-1.5 border rounded-md cursor-pointer transition-all duration-200 {{ $isAvailable ? 'bg-meadow-green/10 border-meadow-green/30 text-meadow-green hover:bg-meadow-green/20' : 'bg-poppy-red/10 border-poppy-red/30 text-poppy-red hover:bg-poppy-red/20' }}"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false"
            >
                <span class="font-mono text-[8px] uppercase font-bold opacity-60 leading-none mb-0.5">{{ $dayName }}</span>
                <span class="font-mono text-xs font-bold leading-none">{{ $dayNum }}</span>

                <!-- Tooltip -->
                <div 
                    x-show="showTooltip" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute z-30 bottom-full mb-1.5 left-1/2 transform -translate-x-1/2 bg-plum-ink text-petal-cream text-[9px] font-mono py-1 px-2 rounded-md border border-petal-cream/10 shadow-xl whitespace-nowrap pointer-events-none"
                    style="display: none;"
                >
                    {{ $carbonDate->translatedFormat('d M Y') }}: {{ $isAvailable ? 'Tersedia' : 'Penuh/Booked' }}
                </div>
            </div>
        @endforeach
    </div>
</div>
