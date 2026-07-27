<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-3xl font-bold italic text-plum-ink leading-tight">
                Kelola Koleksi Item Fisik
            </h2>
            <a href="{{ route('admin.items.create') }}" class="bg-marigold hover:bg-marigold/95 text-plum-ink font-mono text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] transition">
                + Tambah Item Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Alerts Feedback -->
        @if(session('success'))
            <div class="mb-8 p-4 bg-meadow-green/10 border-2 border-meadow-green text-meadow-green rounded-xl font-mono text-xs font-bold uppercase tracking-wider shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border-2 border-plum-ink rounded-2xl shadow-[6px_6px_0px_rgba(44,30,51,1)] overflow-hidden">
            @if($items->isEmpty())
                <p class="font-sans text-sm text-plum-ink/50 text-center py-12">Belum ada item fisik terdaftar di katalog.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans text-sm border-collapse">
                        <thead>
                            <tr class="font-mono text-[10px] uppercase tracking-wider text-plum-ink/70 bg-petal-cream/50 border-b-2 border-plum-ink">
                                <th class="py-4 px-6 w-20">Foto</th>
                                <th class="py-4 px-4">Kode / Nama</th>
                                <th class="py-4 px-4">Kategori</th>
                                <th class="py-4 px-4">Ukuran (Size)</th>
                                <th class="py-4 px-4">Harga & Deposit</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-plum-ink/10">
                            @foreach($items as $item)
                                <tr class="align-middle hover:bg-petal-cream/10 transition-colors">
                                    <td class="py-4 px-6">
                                        @if($item->cover_image_path)
                                            <img src="{{ asset($item->cover_image_path) }}" alt="{{ $item->name }}" class="w-12 h-16 object-cover rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)]">
                                        @else
                                            <div class="w-12 h-16 bg-petal-cream border-2 border-dashed border-plum-ink/30 rounded-lg flex items-center justify-center font-mono text-[9px] text-plum-ink/40">No Img</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="font-mono text-[10px] font-bold bg-marigold/20 text-plum-ink border border-plum-ink/20 px-2 py-0.5 rounded-md block w-fit mb-1.5">
                                            {{ $item->call_code }}
                                        </span>
                                        <div class="font-bold text-plum-ink text-sm">{{ $item->name }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="bg-blossom-pink/20 text-plum-ink text-xs px-2.5 py-1 rounded-full font-semibold border border-plum-ink/10 whitespace-nowrap">
                                            {{ $item->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-plum-ink/80">
                                        {{ $item->size_label }}
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-plum-ink/80 space-y-0.5">
                                        <div class="font-bold">Sewa: Rp{{ number_format($item->base_price, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-plum-ink/60">Deposit: Rp{{ number_format($item->deposit_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <x-stamp-badge :status="$item->status" />
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('admin.items.edit', $item->id) }}" class="bg-white hover:bg-marigold text-plum-ink font-mono text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini dari katalog?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-poppy-red/10 hover:bg-poppy-red text-poppy-red hover:text-white font-mono text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-poppy-red shadow-[2px_2px_0px_rgba(239,71,111,0.2)] hover:shadow-none transition-all">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 border-t border-plum-ink/10 py-4 px-6 font-mono text-xs bg-petal-cream/10">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
