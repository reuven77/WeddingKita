<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl font-bold italic text-plum-ink leading-tight">
            {{ $role === 'admin' ? 'Dashboard Butik' : 'Area Calon Pengantin' }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Alerts Feedback -->
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

        @if($role === 'admin')
            <!-- ===================================================================
                 ADMIN VIEW
                 =================================================================== -->
            
            <!-- Statistics Cards (01-DESIGN.md §4: angka besar mono) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
                <a href="{{ route('admin.items.index') }}" class="block bg-white hover:bg-petal-cream border-2 border-plum-ink p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] transition group">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-2 group-hover:text-sky-ribbon">Total Item Fisik</span>
                    <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $totalItems }} <span class="text-sm font-sans font-normal text-plum-ink/40 group-hover:text-sky-ribbon transition-colors">&rarr;</span></span>
                </a>
                <a href="{{ route('admin.packages.index') }}" class="block bg-white hover:bg-petal-cream border-2 border-plum-ink p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] transition group">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-2 group-hover:text-sky-ribbon">Total Paket</span>
                    <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $totalPackages }} <span class="text-sm font-sans font-normal text-plum-ink/40 group-hover:text-sky-ribbon transition-colors">&rarr;</span></span>
                </a>
                <div class="bg-white border-2 border-plum-ink p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/50 mb-2">Sedang Disewa</span>
                    <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $activeRentalsCount }}</span>
                </div>
                <div class="bg-white border-2 border-plum-ink p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] bg-blossom-pink/20">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/60 mb-2">Fitting Hari Ini</span>
                    <span class="block font-mono text-3xl font-bold text-plum-ink">{{ $fittingsTodayCount }}</span>
                </div>
                <div class="bg-white border-2 border-plum-ink p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] bg-poppy-red/10">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest text-plum-ink/65 mb-2">Denda Outstanding</span>
                    <span class="block font-mono text-xl font-bold text-poppy-red">Rp{{ number_format($totalFinesOutstanding, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white border-2 {{ $pendingPaymentsCount > 0 ? 'border-marigold bg-marigold/10' : 'border-plum-ink' }} p-5 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)]">
                    <span class="block font-mono text-[9px] uppercase font-bold tracking-widest {{ $pendingPaymentsCount > 0 ? 'text-marigold' : 'text-plum-ink/50' }} mb-2">Antrian Bayar</span>
                    <span class="block font-mono text-3xl font-bold {{ $pendingPaymentsCount > 0 ? 'text-marigold' : 'text-plum-ink' }}">{{ $pendingPaymentsCount }}</span>
                    @if($pendingPaymentsCount > 0)
                        <span class="font-mono text-[8px] text-marigold uppercase">Perlu dikonfirmasi</span>
                    @endif
                </div>
            </div>

            <!-- Kalender & Jadwal Fitting Hari Ini -->
            <div class="mb-10 bg-white border-2 border-plum-ink rounded-2xl shadow-[6px_6px_0px_rgba(44,30,51,1)] overflow-hidden">
                <div class="flex items-baseline justify-between p-6 bg-petal-cream/30 border-b-2 border-plum-ink">
                    <h3 class="font-display text-2xl font-bold text-plum-ink italic">Jadwal Fitting Mendatang</h3>
                    <span class="font-mono text-[9px] font-bold text-plum-ink/50 uppercase">Boutique Appointment Queue</span>
                </div>

                @if($fittings->isEmpty())
                    <p class="font-sans text-sm text-plum-ink/50 text-center py-12">Tidak ada jadwal fitting untuk beberapa hari ke depan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-sans text-sm border-collapse">
                            <thead>
                                <tr class="font-mono text-[10px] uppercase tracking-wider text-plum-ink/70 bg-petal-cream/10 border-b border-plum-ink/10">
                                    <th class="py-4 px-6">Tanggal & Waktu</th>
                                    <th class="py-4 px-4">Klien</th>
                                    <th class="py-4 px-4">Busana / Paket</th>
                                    <th class="py-4 px-4">Status</th>
                                    <th class="py-4 px-6 text-right">Aksi Kelola</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-plum-ink/10">
                                @foreach($fittings as $fit)
                                    <tr class="align-middle hover:bg-petal-cream/10 transition-colors">
                                        <td class="py-4 px-6 font-mono text-xs font-semibold">
                                            {{ $fit->scheduled_at->translatedFormat('d M Y · H:i') }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-plum-ink">{{ $fit->user->name }}</div>
                                            <div class="font-mono text-[10px] text-plum-ink/60 mt-0.5">{{ $fit->user->phone }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($fit->item)
                                                <span class="font-mono text-[10px] font-bold bg-marigold/20 border border-plum-ink/20 px-2 py-0.5 rounded-md inline-block mr-1">
                                                    {{ $fit->item->call_code }}
                                                </span>
                                                <span class="text-xs text-plum-ink/80">{{ $fit->item->name }}</span>
                                            @elseif($fit->package)
                                                <span class="text-xs font-semibold text-plum-ink/80">[Paket] {{ $fit->package->name }}</span>
                                            @else
                                                <span class="text-xs text-plum-ink/40">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <x-stamp-badge :status="$fit->status" />
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            @if($fit->status === \App\Models\Fitting::STATUS_TERJADWAL)
                                                <div class="flex justify-end gap-1.5">
                                                    <form action="{{ route('admin.fittings.status', $fit->id) }}" method="POST" onsubmit="return confirm('Tandai jadwal fitting ini sebagai SELESAI? Status transaksi akan berubah ke Menunggu Pembayaran.');">
                                                        @csrf
                                                        <input type="hidden" name="status" value="complete">
                                                        <button type="submit" class="bg-meadow-green hover:bg-meadow-green/95 text-white font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            Fitting Selesai
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.fittings.status', $fit->id) }}" method="POST" onsubmit="return confirm('Tandai member TIDAK HADIR pada jadwal fitting ini?');">
                                                        @csrf
                                                        <input type="hidden" name="status" value="noshow">
                                                        <button type="submit" class="bg-marigold hover:bg-marigold/95 text-plum-ink font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            No Show
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.fittings.status', $fit->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN jadwal fitting ini?');">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancel">
                                                        <button type="submit" class="bg-poppy-red hover:bg-poppy-red/95 text-white font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            Batal
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="font-mono text-[9px] text-plum-ink/40 uppercase font-semibold">Terkunci</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- ═══ MENUNGGU PEMBAYARAN: Fitting selesai, member belum upload bukti ═══ -->
            @if(isset($awaitingPayment) && $awaitingPayment->isNotEmpty())
            <div class="mb-8 bg-white border-2 border-marigold/60 rounded-2xl shadow-[6px_6px_0px_rgba(245,166,35,0.25)] overflow-hidden">
                <div class="flex items-center justify-between p-6 bg-marigold/5 border-b-2 border-marigold/40">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-plum-ink italic">Menunggu Upload Pembayaran</h3>
                        <p class="font-mono text-[10px] text-plum-ink/60 uppercase mt-1">Fitting selesai — member belum mengunggah bukti pembayaran</p>
                    </div>
                    <span class="bg-marigold/20 text-plum-ink font-mono text-xs font-bold px-3 py-1.5 rounded-full border-2 border-plum-ink">{{ $awaitingPayment->count() }} member</span>
                </div>
                <div class="divide-y divide-marigold/10">
                    @foreach($awaitingPayment as $aRent)
                        <div class="p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center hover:bg-marigold/5 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-plum-ink">{{ $aRent->user->name }}</span>
                                    <span class="font-mono text-[9px] bg-plum-ink/5 border border-plum-ink/20 text-plum-ink px-1.5 py-0.5 rounded">{{ $aRent->user->phone ?? $aRent->user->email }}</span>
                                </div>
                                <div class="text-xs text-plum-ink/60 mt-0.5">
                                    {{ $aRent->item?->name ?? $aRent->package?->name ?? '—' }} • Acara: {{ $aRent->event_date->format('d M Y') }}
                                </div>
                                <div class="font-mono text-xs font-bold text-plum-ink mt-1">Tagihan: Rp{{ number_format($aRent->total_price, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $aRent->user->phone ?? '') }}" target="_blank" class="font-mono text-[10px] font-bold uppercase tracking-wider bg-meadow-green/20 text-meadow-green border border-meadow-green px-3 py-2 rounded-xl hover:bg-meadow-green hover:text-white transition-all">
                                    Hubungi Member
                                </a>
                                <a href="{{ route('rentals.invoice', $aRent->id) }}" target="_blank" class="font-mono text-[10px] font-bold uppercase tracking-wider text-plum-ink/60 hover:text-plum-ink border border-plum-ink/20 hover:border-plum-ink px-3 py-2 rounded-xl transition-all">
                                    Draft Nota
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ═══ ANTRIAN PEMBAYARAN: Bukti diupload, perlu dikonfirmasi ═══ -->
            @if($pendingPayments->isNotEmpty())
            <div class="mb-8 bg-white border-2 border-marigold rounded-2xl shadow-[6px_6px_0px_rgba(245,166,35,0.5)] overflow-hidden">
                <div class="flex items-center justify-between p-6 bg-marigold/10 border-b-2 border-marigold">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-plum-ink italic">Antrian Konfirmasi Pembayaran</h3>
                        <p class="font-mono text-[10px] text-plum-ink/60 uppercase mt-1">Bukti transfer sudah diupload — konfirmasi sekarang</p>
                    </div>
                    <span class="bg-marigold text-plum-ink font-mono text-xs font-bold px-3 py-1.5 rounded-full border-2 border-plum-ink">{{ $pendingPayments->count() }} antrian</span>
                </div>
                <div class="divide-y divide-marigold/20">
                    @foreach($pendingPayments as $pRent)
                        <div class="p-5 hover:bg-marigold/5 transition-colors">
                            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                                <!-- Info Penyewa -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-plum-ink text-sm">{{ $pRent->user->name }}</span>
                                        <span class="font-mono text-[9px] bg-marigold/20 border border-marigold/40 text-plum-ink px-1.5 py-0.5 rounded">{{ $pRent->invoiceNumber() }}</span>
                                    </div>
                                    <div class="text-xs text-plum-ink/60">
                                        {{ $pRent->item?->name ?? $pRent->package?->name ?? '—' }} • Acara: {{ $pRent->event_date->format('d M Y') }}
                                    </div>
                                    <div class="font-mono text-sm font-bold text-plum-ink mt-1">
                                        Total: Rp{{ number_format($pRent->total_price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Bukti Bayar -->
                                @if($pRent->payment_proof_path)
                                    <div class="flex-shrink-0">
                                        @php $ext = pathinfo($pRent->payment_proof_path, PATHINFO_EXTENSION); @endphp
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                                            <a href="{{ asset($pRent->payment_proof_path) }}" target="_blank" class="block w-20 h-24 rounded-lg overflow-hidden border-2 border-plum-ink shadow-sm hover:opacity-90 transition">
                                                <img src="{{ asset($pRent->payment_proof_path) }}" alt="Bukti" class="w-full h-full object-cover">
                                            </a>
                                        @else
                                            <a href="{{ asset($pRent->payment_proof_path) }}" target="_blank" class="flex items-center gap-2 font-mono text-xs bg-plum-ink/5 border border-plum-ink/20 px-3 py-2 rounded-lg hover:bg-plum-ink/10">
                                                Lihat PDF
                                            </a>
                                        @endif
                                        <div class="font-mono text-[9px] text-plum-ink/50 text-center mt-1">Bukti Transfer</div>
                                    </div>
                                @endif

                                <!-- Aksi Admin -->
                                <div class="flex flex-col gap-2 flex-shrink-0">
                                    <form action="{{ route('admin.rentals.confirm-payment', $pRent->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa pembayaran transaksi ini sudah LUNAS?');">
                                        @csrf
                                        <button type="submit" class="w-full bg-meadow-green hover:bg-meadow-green/90 text-white font-mono text-[10px] font-bold uppercase tracking-wider px-4 py-2 rounded-xl border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                            Konfirmasi Lunas
                                        </button>
                                    </form>
                                    <a href="{{ route('rentals.invoice', $pRent->id) }}" target="_blank" class="w-full text-center font-mono text-[10px] font-bold uppercase tracking-wider text-plum-ink/60 hover:text-plum-ink border border-plum-ink/20 hover:border-plum-ink px-4 py-1.5 rounded-xl transition-all">
                                        Lihat Nota
                                    </a>
                                </div>
                            </div>
                            @if($pRent->payment_notes)
                                <div class="mt-3 text-xs text-plum-ink/60 italic bg-petal-cream/50 px-3 py-2 rounded-lg border-l-2 border-marigold">
                                    Catatan member: "{{ $pRent->payment_notes }}"
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ═══ MENUNGGU DIAMBIL: Pembayaran dikonfirmasi, baju belum diambil ═══ -->
            @if($awaitingPickup->isNotEmpty())
            <div class="mb-8 bg-white border-2 border-sky-ribbon rounded-2xl shadow-[6px_6px_0px_rgba(75,123,236,0.3)] overflow-hidden">
                <div class="flex items-center justify-between p-6 bg-sky-ribbon/10 border-b-2 border-sky-ribbon">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-plum-ink italic">Siap Diambil</h3>
                        <p class="font-mono text-[10px] text-plum-ink/60 uppercase mt-1">Pembayaran lunas — izinkan pengambilan baju</p>
                    </div>
                    <span class="bg-sky-ribbon text-white font-mono text-xs font-bold px-3 py-1.5 rounded-full border-2 border-plum-ink">{{ $awaitingPickup->count() }}</span>
                </div>
                <div class="divide-y divide-sky-ribbon/20">
                    @foreach($awaitingPickup as $pRent)
                        <div class="p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center hover:bg-sky-ribbon/5 transition-colors">
                            <div class="flex-1">
                                <div class="font-bold text-plum-ink">{{ $pRent->user->name }}</div>
                                <div class="text-xs text-plum-ink/60 mt-0.5">
                                    {{ $pRent->item?->name ?? $pRent->package?->name ?? '—' }} • Acara: {{ $pRent->event_date->format('d M Y') }}
                                </div>
                                <div class="font-mono text-xs font-bold text-sky-ribbon mt-1">{{ $pRent->invoiceNumber() }}</div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <form action="{{ route('admin.rentals.allow-pickup', $pRent->id) }}" method="POST" onsubmit="return confirm('Izinkan pengambilan baju untuk penyewa ini? Status akan menjadi Sedang Disewa.');">
                                    @csrf
                                    <button type="submit" class="bg-sky-ribbon hover:bg-sky-ribbon/90 text-white font-mono text-[10px] font-bold uppercase tracking-wider px-4 py-2 rounded-xl border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                        Izinkan Ambil
                                    </button>
                                </form>
                                <a href="{{ route('rentals.invoice', $pRent->id) }}" target="_blank" class="font-mono text-[10px] font-bold uppercase tracking-wider text-plum-ink/60 hover:text-plum-ink border border-plum-ink/20 hover:border-plum-ink px-4 py-2 rounded-xl transition-all">
                                    Nota
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tabel Transaksi / Penyewaan Butik -->
            <div class="bg-white border-2 border-plum-ink rounded-2xl shadow-[6px_6px_0px_rgba(44,30,51,1)] overflow-hidden">
                <div class="flex items-baseline justify-between p-6 bg-petal-cream/30 border-b-2 border-plum-ink">
                    <h3 class="font-display text-2xl font-bold text-plum-ink italic">Semua Transaksi Penyewaan</h3>
                    <span class="font-mono text-[9px] font-bold text-plum-ink/50 uppercase">Rental Logs</span>
                </div>

                @if($rentals->isEmpty())
                    <p class="font-sans text-sm text-plum-ink/50 text-center py-12">Belum ada transaksi penyewaan tercatat.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-sans text-sm border-collapse">
                            <thead>
                                <tr class="font-mono text-[10px] uppercase tracking-wider text-plum-ink/70 bg-petal-cream/10 border-b border-plum-ink/10">
                                    <th class="py-4 px-6">Penyewa & Acara</th>
                                    <th class="py-4 px-4">Koleksi</th>
                                    <th class="py-4 px-4">Jadwal Sewa</th>
                                    <th class="py-4 px-4">Biaya & Denda</th>
                                    <th class="py-4 px-4">Status</th>
                                    <th class="py-4 px-6 text-right">Aksi Logistik</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-plum-ink/10">
                                @foreach($rentals as $rent)
                                    <tr class="align-middle hover:bg-petal-cream/10 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-plum-ink">{{ $rent->user->name }}</div>
                                            <div class="font-mono text-[10px] text-plum-ink/60 mt-0.5">
                                                Acara: {{ $rent->event_date->translatedFormat('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($rent->item)
                                                <span class="font-mono text-[10px] font-bold bg-marigold/20 border border-plum-ink/20 px-2 py-0.5 rounded-md inline-block mb-1">
                                                    {{ $rent->item->call_code }}
                                                </span>
                                                <span class="text-xs text-plum-ink/80 block">{{ $rent->item->name }}</span>
                                            @elseif($rent->package)
                                                <span class="text-xs font-semibold text-plum-ink/80 block">[Paket] {{ $rent->package->name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs space-y-1">
                                            <div>Pick: {{ $rent->pickup_at->translatedFormat('d M Y (H:i)') }}</div>
                                            <div class="text-plum-ink/60 font-medium">Due: {{ $rent->return_due_at->translatedFormat('d M Y (H:i)') }}</div>
                                            @if($rent->returned_at)
                                                <div class="text-meadow-green font-semibold bg-meadow-green/10 px-1.5 py-0.5 rounded w-fit">Ret: {{ $rent->returned_at->translatedFormat('d M Y (H:i)') }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs space-y-0.5">
                                            <div class="font-bold">Tot: Rp{{ number_format($rent->total_price, 0, ',', '.') }}</div>
                                            @if($rent->fine_amount > 0)
                                                <div class="text-poppy-red font-semibold bg-poppy-red/10 px-1.5 py-0.5 rounded w-fit">Fine: Rp{{ number_format($rent->fine_amount, 0, ',', '.') }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <x-stamp-badge :status="$rent->status" />
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex justify-end gap-1.5">
                                                @if($rent->status === \App\Models\Rental::STATUS_SEDANG_DISEWA || $rent->status === \App\Models\Rental::STATUS_TERLAMBAT)
                                                    <form action="{{ route('admin.rentals.status', $rent->id) }}" method="POST" onsubmit="return confirm('Proses pengembalian busana sewa ini? Denda akan dihitung otomatis jika terlambat.');">
                                                        @csrf
                                                        <input type="hidden" name="status" value="return">
                                                        <button type="submit" class="bg-marigold hover:bg-marigold/95 text-plum-ink font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            Proses Pengembalian
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(in_array($rent->status, [\App\Models\Rental::STATUS_MENUNGGU_FITTING, \App\Models\Rental::STATUS_MENUNGGU_PEMBAYARAN, \App\Models\Rental::STATUS_PEMBAYARAN_DIKONFIRMASI]))
                                                    <form action="{{ route('admin.rentals.status', $rent->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi sewa ini?');">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancel">
                                                        <button type="submit" class="bg-poppy-red hover:bg-poppy-red/95 text-white font-mono text-[9px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            Batalkan
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('rentals.invoice', $rent->id) }}" target="_blank" class="font-mono text-[9px] font-bold uppercase tracking-wider text-plum-ink/60 hover:text-plum-ink border border-plum-ink/20 hover:border-plum-ink px-2.5 py-1.5 rounded-lg transition-all">
                                                    Nota
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 border-t border-plum-ink/10 py-4 px-6 font-mono text-xs bg-petal-cream/10">
                        {{ $rentals->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- ===================================================================
                 MEMBER VIEW
                 =================================================================== -->
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Kiri: Jadwal Fitting Saya -->
                <div class="md:col-span-4 bg-white border-2 border-plum-ink p-6 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] space-y-6">
                    <div class="pb-3 border-b border-plum-ink/10">
                        <h3 class="font-display text-2xl font-bold text-plum-ink italic leading-tight">Jadwal Fitting Saya</h3>
                        <p class="font-sans text-[10px] text-plum-ink/50 mt-1">Datanglah ke butik sesuai jam yang terjadwal.</p>
                    </div>

                    @if($fittings->isEmpty())
                        <p class="font-sans text-xs text-plum-ink/50 text-center py-6">Anda belum memiliki jadwal fitting terdaftar.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($fittings as $fit)
                                <div class="p-4 bg-petal-cream/40 border border-plum-ink/10 rounded-xl space-y-2 relative overflow-hidden">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-[10px] font-bold text-plum-ink/70">
                                            {{ $fit->scheduled_at->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="font-mono text-[9px] font-bold text-plum-ink bg-white border border-plum-ink/20 px-2 py-0.5 rounded">
                                            {{ $fit->scheduled_at->format('H:i') }}
                                        </span>
                                    </div>
                                    
                                    <div class="font-sans text-xs font-semibold text-plum-ink">
                                        Fit: {{ $fit->item->name ?? $fit->package->name ?? 'Koleksi Butik' }}
                                    </div>

                                    <div class="pt-2">
                                        <x-stamp-badge :status="$fit->status" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Kanan: Riwayat Penyewaan Saya -->
                <div class="md:col-span-8 bg-white border-2 border-plum-ink p-6 rounded-2xl shadow-[4px_4px_0px_rgba(44,30,51,1)] space-y-6">
                    <div class="pb-3 border-b border-plum-ink/10 flex justify-between items-baseline">
                        <h3 class="font-display text-2xl font-bold text-plum-ink italic leading-tight">Riwayat Penyewaan Saya</h3>
                        <span class="font-mono text-[9px] text-plum-ink/50 uppercase">Rental History</span>
                    </div>

                    @if($rentals->isEmpty())
                        <div class="text-center py-12 border-2 border-dashed border-plum-ink/10 rounded-xl">
                            <p class="font-sans text-sm text-plum-ink/55 mb-3">Anda belum pernah melakukan pemesanan sewa.</p>
                            <a href="/" class="inline-block font-mono text-[10px] font-bold uppercase tracking-wider bg-marigold border-2 border-plum-ink py-2.5 px-4 rounded-xl shadow-[2px_2px_0px_rgba(44,30,51,1)]">
                                Mulai Cari Busana
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($rentals as $rent)
                                <div class="p-5 border-2 border-plum-ink/10 rounded-2xl bg-gradient-to-br from-white to-petal-cream/10 space-y-4">
                                    
                                    <!-- Header Row -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-plum-ink/5 pb-3">
                                        <div>
                                            <span class="font-mono text-[9px] text-plum-ink/40 uppercase block">Tanggal Order</span>
                                            <span class="font-mono text-xs font-bold text-plum-ink">{{ $rent->created_at->translatedFormat('d M Y · H:i') }}</span>
                                        </div>
                                        <div>
                                            <x-stamp-badge :status="$rent->status" />
                                        </div>
                                    </div>

                                    <!-- Main Info Row -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Busana info -->
                                        <div>
                                            <span class="font-mono text-[9px] text-plum-ink/40 uppercase block mb-1">Busana Pengantin</span>
                                            @if($rent->item)
                                                <a href="{{ route('items.show', $rent->item->id) }}" class="font-sans text-sm font-bold text-plum-ink hover:underline">
                                                    {{ $rent->item->name }} ({{ $rent->item->call_code }})
                                                </a>
                                            @elseif($rent->package)
                                                <a href="{{ route('packages.show', $rent->package->id) }}" class="font-sans text-sm font-bold text-plum-ink hover:underline">
                                                    [Paket] {{ $rent->package->name }}
                                                </a>
                                            @endif
                                            
                                            <!-- Addons list -->
                                            @if($rent->addons->isNotEmpty())
                                                <div class="mt-2 space-y-1">
                                                    <span class="font-mono text-[8px] text-plum-ink/50 uppercase block">Layanan Tambahan:</span>
                                                    @foreach($rent->addons as $addon)
                                                        <span class="inline-block bg-plum-ink/5 border border-plum-ink/10 text-plum-ink/75 text-[9px] px-2 py-0.5 rounded-full font-sans">
                                                            {{ $addon->service->name }} (Rp{{ number_format($addon->price_at_booking, 0, ',', '.') }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Logistical Dates -->
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div>
                                                <span class="font-mono text-[9px] text-plum-ink/40 uppercase block">Ambil H-2</span>
                                                <span class="font-sans font-semibold">{{ $rent->pickup_at->translatedFormat('d M (H:i)') }}</span>
                                            </div>
                                            <div>
                                                <span class="font-mono text-[9px] text-plum-ink/40 uppercase block">Kembali H+1</span>
                                                <span class="font-sans font-semibold">{{ $rent->return_due_at->translatedFormat('d M (H:i)') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Finance Row -->
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-t border-plum-ink/5 pt-3">
                                        <div class="font-mono text-xs">
                                            Total Bayar: <span class="font-bold text-plum-ink">Rp{{ number_format($rent->total_price, 0, ',', '.') }}</span>
                                        </div>

                                        @if($rent->fine_amount > 0)
                                            <div class="font-mono text-xs text-poppy-red font-semibold bg-poppy-red/10 border border-poppy-red/20 px-2 py-1 rounded">
                                                Denda Terlambat: Rp{{ number_format($rent->fine_amount, 0, ',', '.') }} (20K/hari)
                                            </div>
                                        @endif
                                    </div>

                                     <!-- ── GUIDANCE TIMELINE TRACKER ── -->
                                     <div class="mt-4 pt-3 border-t border-plum-ink/5">
                                         <div class="font-mono text-[9px] uppercase font-bold text-plum-ink/50 mb-2">Status & Panduan Langkah:</div>
                                         <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-[9px]">
                                             <!-- Step 1: Fitting -->
                                             <div class="p-2 rounded-lg border {{ in_array($rent->status, ['menunggu_fitting']) ? 'bg-blossom-pink/20 border-plum-ink font-bold text-plum-ink' : 'bg-plum-ink/5 border-plum-ink/10 text-plum-ink/50' }}">
                                                 1. Fitting Butik
                                             </div>
                                             <!-- Step 2: Bayar -->
                                             <div class="p-2 rounded-lg border {{ in_array($rent->status, ['menunggu_pembayaran']) ? 'bg-marigold/20 border-plum-ink font-bold text-plum-ink' : 'bg-plum-ink/5 border-plum-ink/10 text-plum-ink/50' }}">
                                                 2. Pembayaran
                                             </div>
                                             <!-- Step 3: Pengambilan -->
                                             <div class="p-2 rounded-lg border {{ in_array($rent->status, ['pembayaran_dikonfirmasi', 'sedang_disewa']) ? 'bg-meadow-green/20 border-plum-ink font-bold text-plum-ink' : 'bg-plum-ink/5 border-plum-ink/10 text-plum-ink/50' }}">
                                                 3. Pengambilan
                                             </div>
                                             <!-- Step 4: Selesai/Pengembalian -->
                                             <div class="p-2 rounded-lg border {{ in_array($rent->status, ['dikembalikan', 'terlambat']) ? 'bg-sky-ribbon/20 border-plum-ink font-bold text-plum-ink' : 'bg-plum-ink/5 border-plum-ink/10 text-plum-ink/50' }}">
                                                 4. Pengembalian
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Member Cancel Option (Only for Waiting Fitting) -->
                                     @if($rent->status === \App\Models\Rental::STATUS_MENUNGGU_FITTING)
                                         <div class="mt-3 flex justify-end">
                                             <form action="{{ route('rentals.cancel', $rent->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan sewa ini?');">
                                                 @csrf
                                                 <button type="submit" class="font-mono text-[9px] font-bold uppercase tracking-wider text-poppy-red hover:underline">
                                                     Batalkan Pesanan Ini
                                                 </button>
                                             </form>
                                         </div>
                                     @endif

                                    <!-- ── PAYMENT SECTION ── -->
                                    @if($rent->status === \App\Models\Rental::STATUS_MENUNGGU_PEMBAYARAN)
                                        <div class="mt-4 rounded-2xl border-2 border-marigold bg-marigold/8 overflow-hidden">
                                            <div class="bg-marigold/20 px-4 py-3 flex items-center justify-between">
                                                <div>
                                                    <span class="font-mono text-[10px] font-bold text-plum-ink uppercase tracking-wider">Tagihan Pembayaran</span>
                                                    <div class="font-mono text-lg font-bold text-plum-ink mt-0.5">Rp{{ number_format($rent->total_price, 0, ',', '.') }}</div>
                                                </div>
                                                @if($rent->payment_status === \App\Models\Rental::PAYMENT_MENUNGGU_KONFIRMASI)
                                                    <div class="text-right">
                                                        <span class="inline-block bg-marigold/30 border border-marigold text-plum-ink font-mono text-[9px] font-bold uppercase px-2 py-1 rounded-lg">Menunggu Konfirmasi Admin</span>
                                                        @if($rent->payment_proof_path)
                                                            <div class="mt-1 font-mono text-[9px] text-plum-ink/50">Bukti sudah diupload</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            @if($rent->payment_status === \App\Models\Rental::PAYMENT_MENUNGGU)
                                                <div class="p-4">
                                                    <p class="text-xs text-plum-ink/70 mb-3">Upload bukti transfer/pembayaran Anda untuk dikonfirmasi admin.</p>
                                                    <form action="{{ route('rentals.payment.submit', $rent->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                                        @csrf
                                                        <div class="flex flex-col gap-1.5">
                                                            <label class="font-mono text-[10px] uppercase font-bold tracking-wider text-plum-ink">Bukti Transfer / Struk Pembayaran</label>
                                                            <input type="file" name="payment_proof" accept="image/*,.pdf" required
                                                                class="w-full border-2 border-dashed border-marigold rounded-xl px-3 py-2 font-sans text-xs focus:outline-none text-plum-ink">
                                                            <p class="text-[9px] text-plum-ink/45 font-mono">Format: JPG, PNG, atau PDF. Maks 5MB.</p>
                                                        </div>
                                                        <div class="flex flex-col gap-1.5">
                                                            <label class="font-mono text-[10px] uppercase font-bold tracking-wider text-plum-ink">Catatan (opsional)</label>
                                                            <input type="text" name="payment_notes" placeholder="Contoh: Transfer BCA jam 14:30" maxlength="200"
                                                                class="w-full border-2 border-plum-ink/20 rounded-xl px-3 py-2 font-sans text-xs focus:outline-none focus:border-marigold">
                                                        </div>
                                                        <button type="submit" class="w-full bg-marigold hover:bg-marigold/90 text-plum-ink font-mono text-xs font-bold uppercase tracking-wider py-2.5 px-4 rounded-xl border-2 border-plum-ink shadow-[2px_2px_0px_rgba(44,30,51,1)] hover:shadow-none transition-all">
                                                            Upload Bukti Pembayaran
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($rent->payment_status === \App\Models\Rental::PAYMENT_MENUNGGU_KONFIRMASI)
                                                <div class="p-4">
                                                    <p class="text-xs text-plum-ink/60">Admin sedang memverifikasi bukti pembayaran Anda. Halaman ini akan diperbarui setelah konfirmasi.</p>
                                                    <form action="{{ route('rentals.payment.submit', $rent->id) }}" method="POST" enctype="multipart/form-data" class="mt-3 flex items-center gap-2">
                                                        @csrf
                                                        <input type="file" name="payment_proof" accept="image/*,.pdf"
                                                            class="flex-1 border-2 border-dashed border-plum-ink/20 rounded-xl px-2 py-1.5 font-sans text-xs focus:outline-none">
                                                        <button type="submit" class="font-mono text-[10px] uppercase font-bold text-plum-ink/60 hover:text-plum-ink border border-plum-ink/20 hover:border-plum-ink px-3 py-1.5 rounded-lg transition">
                                                            Upload Ulang
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if(in_array($rent->status, [\App\Models\Rental::STATUS_PEMBAYARAN_DIKONFIRMASI, \App\Models\Rental::STATUS_SEDANG_DISEWA, \App\Models\Rental::STATUS_DIKEMBALIKAN, \App\Models\Rental::STATUS_TERLAMBAT]))
                                        <div class="mt-4 flex items-center gap-3">
                                            @if($rent->status === \App\Models\Rental::STATUS_PEMBAYARAN_DIKONFIRMASI)
                                                <div class="flex-1 bg-meadow-green/10 border border-meadow-green rounded-xl px-4 py-2.5 font-mono text-xs text-meadow-green font-bold">
                                                    Pembayaran dikonfirmasi! Baju siap diambil di butik.
                                                </div>
                                            @endif
                                            @if($rent->payment_status === \App\Models\Rental::PAYMENT_LUNAS)
                                                <a href="{{ route('rentals.invoice', $rent->id) }}" target="_blank"
                                                    class="flex items-center gap-1.5 font-mono text-[10px] font-bold uppercase tracking-wider text-plum-ink border-2 border-plum-ink hover:bg-plum-ink hover:text-white px-3 py-2 rounded-xl transition-all shadow-[2px_2px_0px_rgba(44,30,51,0.3)] hover:shadow-none">
                                                    Cetak Nota
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        @endif

    </div>
</x-app-layout>
