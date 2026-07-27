<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.packages.index') }}" class="font-mono text-xs font-bold text-plum-ink/65 hover:text-plum-ink">&larr; Kembali</a>
            <h2 class="font-display text-3xl font-bold italic text-plum-ink leading-tight">
                Edit Paket: {{ $package->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white border-2 border-plum-ink rounded-2xl p-8 shadow-[6px_6px_0px_rgba(44,30,51,1)]">
            <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="space-y-6">
                    <h3 class="font-display text-xl font-bold text-plum-ink italic border-b pb-2 border-plum-ink/10">1. Informasi Umum</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <label for="name" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Nama Paket</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                            @error('name')
                                <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-1 flex flex-col gap-2">
                            <label for="category_id" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Kategori</label>
                            <select name="category_id" id="category_id" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $package->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="description" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Deskripsi Paket</label>
                        <textarea name="description" id="description" rows="3" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-sans text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex flex-col gap-2">
                            <label for="base_price" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Harga Dasar Paket (Rp)</label>
                            <input type="number" name="base_price" id="base_price" value="{{ old('base_price', (int)$package->base_price) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-mono text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                            @error('base_price')
                                <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="deposit_amount" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Jaminan / Deposit (Rp)</label>
                            <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', (int)$package->deposit_amount) }}" class="w-full border-2 border-plum-ink rounded-xl px-4 py-2.5 font-mono text-sm focus:outline-none focus:ring-0 focus:border-sky-ribbon">
                            @error('deposit_amount')
                                <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="cover_image" class="block font-mono text-xs uppercase font-bold tracking-wider text-plum-ink">Ganti Cover Gambar</label>
                            <input type="file" name="cover_image" id="cover_image" class="w-full border-2 border-dashed border-plum-ink/30 rounded-xl px-3 py-2 font-sans text-xs focus:outline-none">
                            @if($package->cover_image_path)
                                <div class="mt-2 flex items-center gap-2">
                                    <img src="{{ asset($package->cover_image_path) }}" alt="Preview" class="w-10 h-12 object-cover rounded border border-plum-ink/20">
                                    <span class="font-sans text-[10px] text-plum-ink/50">Foto saat ini</span>
                                </div>
                            @endif
                            @error('cover_image')
                                <p class="text-poppy-red font-mono text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Select Items -->
                <div class="space-y-4">
                    <div class="flex items-baseline justify-between border-b pb-2 border-plum-ink/10">
                        <h3 class="font-display text-xl font-bold text-plum-ink italic">2. Pilih Item Fisik Koleksi</h3>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-plum-ink/50">Wajib pilih minimal satu gaun/jas/aksesoris</span>
                    </div>
                    @error('items')
                        <p class="text-poppy-red font-mono text-[10px] mb-2">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-72 overflow-y-auto p-2 border-2 border-plum-ink/10 rounded-xl">
                        @foreach($items as $item)
                            <label class="flex items-center gap-3 p-3 bg-petal-cream/20 border border-plum-ink/10 rounded-lg cursor-pointer hover:bg-petal-cream/40 transition">
                                <input type="checkbox" name="items[]" value="{{ $item->id }}" {{ in_array($item->id, old('items', $attachedItems)) ? 'checked' : '' }} class="rounded border-plum-ink text-marigold focus:ring-marigold">
                                <div class="text-xs">
                                    <span class="font-mono font-bold bg-white px-1.5 py-0.5 rounded border text-[10px] text-plum-ink/75 mr-1">{{ $item->call_code }}</span>
                                    <span class="font-semibold text-plum-ink">{{ $item->name }}</span>
                                    <span class="block text-[10px] text-plum-ink/50 mt-0.5">{{ $item->category->name }} (Size: {{ $item->size_label }})</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Select Services / Addons -->
                <div class="space-y-4">
                    <div class="flex items-baseline justify-between border-b pb-2 border-plum-ink/10">
                        <h3 class="font-display text-xl font-bold text-plum-ink italic">3. Hubungkan Jasa & Layanan</h3>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-plum-ink/50">Default = Otomatis Termasuk di Harga Paket</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($services as $srv)
                            @php
                                $isSelected = in_array($srv->id, old('services', $attachedServices));
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-plum-ink/10 rounded-xl bg-white shadow-sm" x-data="{ selected: {{ $isSelected ? 'true' : 'false' }} }">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="services[]" value="{{ $srv->id }}" @change="selected = $el.checked" {{ $isSelected ? 'checked' : '' }} class="rounded border-plum-ink text-sky-ribbon focus:ring-sky-ribbon">
                                    <div>
                                        <div class="font-semibold text-xs text-plum-ink">{{ $srv->name }}</div>
                                        <div class="text-[10px] font-mono text-plum-ink/55">Biaya tambahan: Rp{{ number_format($srv->price, 0, ',', '.') }}</div>
                                    </div>
                                </label>
                                
                                <div class="flex items-center gap-2" x-show="selected" x-transition>
                                    <input type="checkbox" name="default_services[]" value="{{ $srv->id }}" id="def_{{ $srv->id }}" {{ in_array($srv->id, old('default_services', $defaultServices)) ? 'checked' : '' }} class="rounded border-plum-ink text-meadow-green focus:ring-meadow-green">
                                    <label for="def_{{ $srv->id }}" class="font-mono text-[10px] font-bold text-meadow-green uppercase tracking-wider">Set as Default</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-marigold hover:bg-marigold/95 text-plum-ink font-mono text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-xl border-2 border-plum-ink shadow-[3px_3px_0px_rgba(44,30,51,1)] transition">
                        Perbarui Paket
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
