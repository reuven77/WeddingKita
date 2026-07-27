@props([
    'availableSlots' => [], // Key: '09:00', Value: remaining slots (0-5)
    'selectedDate' => null,  // Carbon date
    'itemId' => null,
    'packageId' => null
])

@php
    $selectedDate = $selectedDate ?? \Carbon\Carbon::today();
@endphp

<div 
    x-data="{ 
        selectedTime: '',
        date: '{{ $selectedDate->toDateString() }}',
        slots: {{ json_encode($availableSlots) }}
    }" 
    class="p-5 bg-white border-2 border-plum-ink rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] bg-gradient-to-br from-white to-petal-cream/20"
>
    <!-- Header -->
    <div class="mb-4 pb-4 border-b border-plum-ink/10">
        <h3 class="font-display text-2xl font-bold text-plum-ink leading-tight">Jadwalkan Sesi Fitting</h3>
        <p class="font-sans text-xs text-plum-ink/60 mt-1">Durasi fitting standar 60 menit didampingi asisten pribadi.</p>
    </div>

    <!-- Date selector (shows next 7 days as beautiful tabs) -->
    <div class="mb-6">
        <label class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-2">Pilih Hari</label>
        <div class="grid grid-cols-7 gap-1">
            @for ($i = 0; $i < 7; $i++)
                @php
                    $day = \Carbon\Carbon::today()->addDays($i);
                    $dayStr = $day->toDateString();
                    $isActive = $day->isSameDay($selectedDate);
                @endphp
                @php
                    $eventDateQuery = request('event_date') ? '&event_date=' . request('event_date') : '';
                @endphp
                <a 
                    href="?date={{ $dayStr }}{{ $itemId ? '&item_id=' . $itemId : '' }}{{ $packageId ? '&package_id=' . $packageId : '' }}{{ $eventDateQuery }}"
                    class="flex flex-col items-center justify-center py-2.5 px-1 border-2 rounded-xl transition-all duration-200 {{ $isActive ? 'bg-marigold border-plum-ink text-plum-ink font-bold shadow-[2px_2px_0px_rgba(44,30,51,1)]' : 'bg-petal-cream/40 border-plum-ink/10 hover:border-plum-ink text-plum-ink/80 hover:bg-petal-cream' }}"
                >
                    <span class="font-mono text-[9px] uppercase leading-none opacity-60 mb-1.5">{{ substr($day->locale('id')->dayName, 0, 3) }}</span>
                    <span class="font-mono text-sm leading-none">{{ $day->format('d') }}</span>
                </a>
            @endfor
        </div>
    </div>

    <!-- Slots Selector -->
    <div class="mb-2">
        <label class="block font-mono text-[10px] uppercase font-bold tracking-widest text-plum-ink/60 mb-2">Pilih Jam Coba</label>
        <div class="grid grid-cols-4 gap-2">
            <template x-for="(remaining, time) in slots" :key="time">
                <button 
                    type="button"
                    :disabled="remaining <= 0"
                    @click="selectedTime = time"
                    :class="{
                        'bg-marigold border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] font-bold text-plum-ink': selectedTime === time,
                        'bg-petal-cream border-plum-ink/10 hover:border-plum-ink text-plum-ink': selectedTime !== time && remaining > 0,
                        'bg-poppy-red/10 border-poppy-red/20 text-poppy-red/40 cursor-not-allowed': remaining <= 0
                    }"
                    class="flex flex-col items-center justify-center py-3 px-1 border-2 rounded-xl transition-all duration-200"
                >
                    <span class="font-mono text-sm leading-none" x-text="time"></span>
                    <span class="font-mono text-[8px] mt-1" :class="remaining <= 0 ? 'text-poppy-red/60 font-semibold' : 'text-plum-ink/50'">
                        <span x-text="remaining <= 0 ? 'Full' : remaining + ' Ready'"></span>
                    </span>
                </button>
            </template>
        </div>
    </div>

    <!-- Hidden inputs for form submit -->
    <input type="hidden" name="scheduled_date" value="{{ $selectedDate->toDateString() }}">
    <input type="hidden" name="scheduled_time" x-model="selectedTime">
</div>
