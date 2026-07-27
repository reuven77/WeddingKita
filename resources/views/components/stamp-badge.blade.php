@props([
    'status' => 'tersedia'
])

@php
    $status = strtolower($status);
    $bgColor = match($status) {
        'tersedia', 'aktif', 'selesai' => 'bg-marigold',
        'menunggu_fitting', 'fitting', 'terjadwal' => 'bg-blossom-pink',
        'menunggu_pembayaran' => 'bg-marigold',
        'pembayaran_dikonfirmasi', 'dikonfirmasi', 'sedang_disewa', 'dikembalikan' => 'bg-meadow-green',
        'terlambat', 'dibatalkan', 'batal', 'tidak_hadir' => 'bg-poppy-red',
        default => 'bg-sky-ribbon',
    };

    // Teks putih untuk background gelap/merah (Poppy Red, Sky Ribbon)
    // Teks Plum Ink untuk background terang jenuh (Marigold, Blossom Pink, Meadow Green)
    // Sesuai dengan aturan aksesibilitas 01-DESIGN.md §6
    $textColor = match($status) {
        'terlambat', 'dibatalkan', 'batal', 'tidak_hadir', 'default' => 'text-white',
        default => 'text-plum-ink',
    };
@endphp

<span class="inline-block transform -rotate-2 select-none border-2 border-plum-ink px-4 py-1 rounded-full font-mono text-[10px] font-bold tracking-widest uppercase shadow-[2px_2px_0px_rgba(44,30,51,1)] {{ $bgColor }} {{ $textColor }}">
    {{ str_replace('_', ' ', $status) }}
</span>
