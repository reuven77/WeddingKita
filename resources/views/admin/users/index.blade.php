<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display text-3xl font-bold italic text-plum-ink leading-tight">
                    Manajemen Akun Member
                </h2>
                <p class="font-mono text-[10px] text-plum-ink/50 uppercase mt-1">
                    {{ $users->count() }} pengguna terdaftar
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Alerts Feedback --}}
        @if(session('success'))
            <div class="mb-8 p-4 bg-meadow-green/10 border-2 border-meadow-green text-meadow-green rounded-xl font-mono text-xs font-bold uppercase tracking-wider shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-8 p-4 bg-poppy-red/10 border-2 border-poppy-red text-poppy-red rounded-xl font-mono text-xs font-bold uppercase tracking-wider shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border-2 border-plum-ink p-4 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Total Member</span>
                <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $users->count() }}</span>
            </div>
            <div class="bg-white border-2 border-plum-ink p-4 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Punya Transaksi</span>
                <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $users->where('rentals_count', '>', 0)->count() }}</span>
            </div>
            <div class="bg-white border-2 border-plum-ink p-4 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Belum Transaksi</span>
                <span class="block font-mono text-3xl font-bold text-plum-ink/50">{{ $users->where('rentals_count', 0)->count() }}</span>
            </div>
            <div class="bg-white border-2 border-plum-ink p-4 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-1">Total Fitting</span>
                <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $users->sum('fittings_count') }}</span>
            </div>
        </div>

        {{-- User Table --}}
        <div class="bg-white border-2 border-plum-ink rounded-2xl shadow-[6px_6px_0px_rgba(44,30,51,1)] overflow-hidden">
            <div class="flex items-center justify-between p-6 bg-petal-cream/30 border-b-2 border-plum-ink">
                <h3 class="font-display text-2xl font-bold text-plum-ink italic">Daftar Member</h3>
                <span class="font-mono text-[9px] font-bold text-plum-ink/50 uppercase">User Registry</span>
            </div>

            @if($users->isEmpty())
                <div class="text-center py-16">
                    <p class="font-sans text-sm text-plum-ink/40">Belum ada member terdaftar di platform.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans text-sm border-collapse">
                        <thead>
                            <tr class="font-mono text-[10px] uppercase tracking-wider text-plum-ink/70 bg-petal-cream/10 border-b border-plum-ink/10">
                                <th class="py-4 px-6">Nama & Email</th>
                                <th class="py-4 px-4">Transaksi</th>
                                <th class="py-4 px-4">Fitting</th>
                                <th class="py-4 px-4">Bergabung</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-plum-ink/10">
                            @foreach($users as $member)
                                <tr class="align-middle hover:bg-red-50/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-plum-ink">{{ $member->name }}</div>
                                        <div class="font-mono text-[10px] text-plum-ink/50 mt-0.5">{{ $member->email }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($member->rentals_count > 0)
                                            <span class="inline-flex items-center gap-1 font-mono text-xs font-bold text-plum-ink bg-marigold/20 border border-marigold/40 px-2.5 py-1 rounded-full">
                                                {{ $member->rentals_count }} transaksi
                                            </span>
                                        @else
                                            <span class="font-mono text-xs text-plum-ink/30">0 transaksi</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($member->fittings_count > 0)
                                            <span class="inline-flex items-center gap-1 font-mono text-xs font-bold text-plum-ink bg-blossom-pink/20 border border-blossom-pink/40 px-2.5 py-1 rounded-full">
                                                {{ $member->fittings_count }} jadwal
                                            </span>
                                        @else
                                            <span class="font-mono text-xs text-plum-ink/30">0 jadwal</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-mono text-[10px] text-plum-ink/60">
                                        {{ $member->created_at->translatedFormat('d M Y') }}
                                        <div class="text-plum-ink/40 text-[9px] mt-0.5">{{ $member->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <form
                                            action="{{ route('admin.users.destroy', $member->id) }}"
                                            method="POST"
                                            onsubmit="return confirmDelete('{{ addslashes($member->name) }}', '{{ $member->email }}', {{ $member->rentals_count }}, {{ $member->fittings_count }})"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 bg-white hover:bg-poppy-red hover:text-white text-poppy-red font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-poppy-red/60 hover:border-poppy-red shadow-sm hover:shadow-[2px_2px_0px_rgba(44,30,51,1)] transition-all duration-150"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Hapus Akun
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- Konfirmasi hapus yang lebih informatif lewat JS --}}
    <script>
        function confirmDelete(name, email, rentalsCount, fittingsCount) {
            let msg = `⚠️ HAPUS AKUN MEMBER\n\n`;
            msg += `Anda akan menghapus:\n`;
            msg += `  • ${name}\n`;
            msg += `  • ${email}\n\n`;

            if (rentalsCount > 0 || fittingsCount > 0) {
                msg += `Data yang IKUT TERHAPUS:\n`;
                if (rentalsCount > 0) msg += `  • ${rentalsCount} transaksi sewa\n`;
                if (fittingsCount > 0) msg += `  • ${fittingsCount} jadwal fitting\n`;
                msg += `\n`;
            } else {
                msg += `Member ini belum memiliki transaksi atau fitting.\n\n`;
            }

            msg += `Tindakan ini TIDAK DAPAT DIBATALKAN.\nLanjutkan?`;

            return confirm(msg);
        }
    </script>
</x-app-layout>
