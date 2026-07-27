<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.items.index') }}" class="font-mono text-xs font-bold text-plum-ink/65 hover:text-plum-ink">&larr; Kembali</a>
            <h2 class="font-display text-3xl font-bold italic text-plum-ink leading-tight">
                Edit Item: {{ $item->call_code }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white border-2 border-plum-ink rounded-2xl p-8 shadow-[6px_6px_0px_rgba(44,30,51,1)]">
            <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Category -->
                <div class="flex flex-col gap-2">
                    <label for="category_id" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Kategori Koleksi</label>
                    <select name="category_id" id="category_id" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Call Code & Name -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 flex flex-col gap-2">
                        <label for="call_code" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Call Code</label>
                        <input type="text" name="call_code" id="call_code" value="{{ old('call_code', $item->call_code) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-mono text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                        @error('call_code')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 flex flex-col gap-2">
                        <label for="name" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Nama Busana / Item</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                        @error('name')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Size Label -->
                <div class="flex flex-col gap-2">
                    <label for="size_label" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Label Ukuran (Size)</label>
                    <input type="text" name="size_label" id="size_label" value="{{ old('size_label', $item->size_label) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                    @error('size_label')
                        <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="flex flex-col gap-2">
                    <label for="description" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Deskripsi Lengkap</label>
                    <textarea name="description" id="description" rows="4" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prices -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label for="base_price" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Harga Sewa Dasar (Rp)</label>
                        <input type="number" name="base_price" id="base_price" value="{{ old('base_price', (int)$item->base_price) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-mono text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                        @error('base_price')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="deposit_amount" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Jaminan / Deposit (Rp)</label>
                        <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', (int)$item->deposit_amount) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-mono text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                        @error('deposit_amount')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status & Image -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label for="status" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Status Ketersediaan</label>
                        <select name="status" id="status" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                            <option value="aktif" {{ old('status', $item->status) == 'aktif' ? 'selected' : '' }}>Aktif (Dapat Disewa)</option>
                            <option value="maintenance" {{ old('status', $item->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="nonaktif" {{ old('status', $item->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="cover_image" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Ganti Foto Busana</label>
                        <input type="file" name="cover_image" id="cover_image" class="w-full border-2 border-dashed border-plum-ink/30 rounded-xl px-3 py-2 font-sans text-xs focus:outline-none">
                        @if($item->cover_image_path)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset($item->cover_image_path) }}" alt="Preview" class="w-10 h-12 object-cover rounded border border-plum-ink/20">
                                <span class="font-sans text-[10px] text-plum-ink/50">Foto saat ini</span>
                            </div>
                        @endif
                        @error('cover_image')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-marigold hover:bg-marigold/95 text-plum-ink font-mono text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-xl border-2 border-plum-ink shadow-[3px_3px_0px_rgba(44,30,51,1)] transition">
                        Perbarui Item
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
