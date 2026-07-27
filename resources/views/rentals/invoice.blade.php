<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran {{ $rental->invoiceNumber() }} — WeddingKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Space+Mono:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --plum-ink: #2C1E33;
            --marigold: #F5A623;
            --petal-cream: #FAF7F2;
            --meadow-green: #4CAF7A;
            --poppy-red: #E05252;
            --sky-ribbon: #4B7BEC;
            --blossom-pink: #F4A0B5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #F0EDE8;
            color: var(--plum-ink);
            min-height: 100vh;
            padding: 2rem;
        }

        .no-print {
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
            align-items: center;
        }

        .btn-print {
            background: var(--plum-ink);
            color: white;
            font-family: 'Space Mono', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            box-shadow: 3px 3px 0 rgba(44,30,51,0.3);
            transition: all 0.2s;
        }
        .btn-print:hover { transform: translate(-1px, -1px); box-shadow: 4px 4px 0 rgba(44,30,51,0.4); }

        .btn-back {
            font-family: 'Space Mono', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            border: 2px solid var(--plum-ink);
            color: var(--plum-ink);
            text-decoration: none;
            background: white;
            box-shadow: 3px 3px 0 rgba(44,30,51,0.2);
            transition: all 0.2s;
        }
        .btn-back:hover { transform: translate(-1px, -1px); }

        /* ── Invoice Paper ── */
        .invoice {
            max-width: 720px;
            margin: 0 auto;
            background: white;
            border: 2px solid var(--plum-ink);
            border-radius: 1.25rem;
            box-shadow: 8px 8px 0 rgba(44,30,51,0.15);
            overflow: hidden;
        }

        /* Header Band */
        .invoice-header {
            background: var(--plum-ink);
            padding: 2rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 2rem;
            color: var(--marigold);
            line-height: 1;
        }
        .brand-sub {
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: white;
            opacity: 0.6;
            margin-top: 0.35rem;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-number {
            font-family: 'Space Mono', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--marigold);
            letter-spacing: 0.05em;
        }
        .invoice-label {
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: white;
            opacity: 0.5;
            margin-bottom: 0.25rem;
        }

        /* Paid Stamp */
        .paid-stamp {
            position: relative;
            display: inline-block;
            margin-top: 0.5rem;
            border: 2px solid var(--meadow-green);
            border-radius: 0.5rem;
            padding: 0.25rem 0.75rem;
            transform: rotate(-3deg);
        }
        .paid-stamp-text {
            font-family: 'Space Mono', monospace;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--meadow-green);
        }

        /* Body */
        .invoice-body {
            padding: 2rem 2.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 1.5rem;
            background: var(--petal-cream);
            border-radius: 0.75rem;
            border: 1px solid rgba(44,30,51,0.08);
            margin-bottom: 1.75rem;
        }
        .info-block-label {
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(44,30,51,0.45);
            margin-bottom: 0.35rem;
        }
        .info-block-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--plum-ink);
            line-height: 1.4;
        }
        .info-block-sub {
            font-size: 0.75rem;
            color: rgba(44,30,51,0.55);
            margin-top: 0.2rem;
        }

        /* Section Title */
        .section-title {
            font-family: 'Space Mono', monospace;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(44,30,51,0.4);
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(44,30,51,0.08);
        }

        /* Item Table */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .item-table th {
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(44,30,51,0.45);
            text-align: left;
            padding: 0.5rem 0.75rem;
            background: var(--petal-cream);
            border-bottom: 1px solid rgba(44,30,51,0.1);
        }
        .item-table th:last-child { text-align: right; }
        .item-table td {
            padding: 0.85rem 0.75rem;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(44,30,51,0.06);
            vertical-align: top;
        }
        .item-table td:last-child { text-align: right; font-family: 'Space Mono', monospace; }
        .item-name { font-weight: 600; }
        .item-sub { font-size: 0.7rem; color: rgba(44,30,51,0.5); margin-top: 0.2rem; }

        /* Totals */
        .totals {
            border-top: 2px solid var(--plum-ink);
            padding-top: 1rem;
            margin-bottom: 1.5rem;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.35rem 0;
        }
        .total-label {
            font-size: 0.8rem;
            color: rgba(44,30,51,0.6);
        }
        .total-value {
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
            color: var(--plum-ink);
        }
        .total-row.grand-total {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--plum-ink);
            border-radius: 0.5rem;
        }
        .total-row.grand-total .total-label {
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .total-row.grand-total .total-value {
            color: var(--marigold);
            font-size: 1.1rem;
            font-weight: 700;
        }

        /* Payment Info */
        .payment-confirmed-box {
            background: rgba(76,175,122,0.08);
            border: 1.5px solid var(--meadow-green);
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .payment-confirmed-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .payment-confirmed-text {
            font-size: 0.8rem;
            color: var(--meadow-green);
            font-weight: 600;
        }
        .payment-confirmed-sub {
            font-size: 0.7rem;
            color: rgba(76,175,122,0.8);
            margin-top: 0.2rem;
        }

        /* Footer */
        .invoice-footer {
            background: var(--petal-cream);
            padding: 1.25rem 2.5rem;
            border-top: 1px solid rgba(44,30,51,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-note {
            font-size: 0.7rem;
            color: rgba(44,30,51,0.4);
            font-style: italic;
        }
        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 0.9rem;
            color: var(--plum-ink);
            opacity: 0.5;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px dashed rgba(44,30,51,0.15);
            margin: 1.25rem 0;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            font-family: 'Space Mono', monospace;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.25rem 0.65rem;
            border-radius: 2rem;
            font-weight: 700;
        }
        .badge-green { background: rgba(76,175,122,0.15); color: var(--meadow-green); border: 1px solid var(--meadow-green); }
        .badge-yellow { background: rgba(245,166,35,0.15); color: #D4851A; border: 1px solid var(--marigold); }
        .badge-blue { background: rgba(75,123,236,0.15); color: var(--sky-ribbon); border: 1px solid var(--sky-ribbon); }
        .badge-red { background: rgba(224,82,82,0.15); color: var(--poppy-red); border: 1px solid var(--poppy-red); }

        /* ── Print Styles ── */
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .invoice {
                box-shadow: none;
                border-radius: 0;
                border: none;
                max-width: 100%;
            }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ url()->previous() }}" class="btn-back">← Kembali</a>
        <button class="btn-print" onclick="window.print()">Cetak Nota</button>
        <a href="{{ route('dashboard') }}" class="btn-back">Dashboard</a>
    </div>

    <div class="invoice">

        <!-- ── Header ── -->
        <div class="invoice-header">
            <div>
                <div class="brand-name">WeddingKita</div>
                <div class="brand-sub">Butik Sewa Busana Pengantin</div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-label">Nomor Nota</div>
                <div class="invoice-number">{{ $rental->invoiceNumber() }}</div>
                <div class="invoice-label" style="margin-top: 0.5rem;">Tanggal Terbit</div>
                <div style="font-family: 'Space Mono', monospace; font-size: 0.75rem; color: rgba(255,255,255,0.7);">
                    {{ ($rental->payment_confirmed_at ?? $rental->updated_at)->format('d M Y, H:i') }} WIB
                </div>
                @if($rental->payment_status === 'lunas')
                    <div class="paid-stamp" style="margin-top: 0.75rem;">
                        <div class="paid-stamp-text">✓ LUNAS</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ── Body ── -->
        <div class="invoice-body">

            <!-- Konfirmasi Pembayaran -->
            @if($rental->payment_status === 'lunas')
                <div class="payment-confirmed-box">
                    <div>
                        <div class="payment-confirmed-text">Pembayaran Telah Dikonfirmasi</div>
                        <div class="payment-confirmed-sub">
                            Dikonfirmasi oleh: {{ $rental->paymentConfirmedBy?->name ?? 'Admin WeddingKita' }}
                            pada {{ $rental->payment_confirmed_at?->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Penyewa & Pesanan -->
            <div class="section-title">Informasi Penyewa & Pesanan</div>
            <div class="info-grid">
                <div>
                    <div class="info-block-label">Nama Penyewa</div>
                    <div class="info-block-value">{{ $rental->user->name }}</div>
                    <div class="info-block-sub">{{ $rental->user->email }}</div>
                    @if($rental->user->phone)
                        <div class="info-block-sub">{{ $rental->user->phone }}</div>
                    @endif
                </div>
                <div>
                    <div class="info-block-label">Tanggal Acara</div>
                    <div class="info-block-value">{{ $rental->event_date->format('d M Y') }}</div>
                    <div class="info-block-sub">
                        Ambil: {{ $rental->pickup_at->format('d M Y, H:i') }} WIB
                    </div>
                    <div class="info-block-sub">
                        Kembali: {{ $rental->return_due_at->format('d M Y, H:i') }} WIB
                    </div>
                </div>
                <div>
                    <div class="info-block-label">Status Sewa</div>
                    <div class="info-block-value">
                        @php
                            $statusLabel = match($rental->status) {
                                'menunggu_fitting'        => ['Menunggu Fitting', 'badge-yellow'],
                                'menunggu_pembayaran'     => ['Menunggu Pembayaran', 'badge-yellow'],
                                'pembayaran_dikonfirmasi' => ['Pembayaran Dikonfirmasi', 'badge-green'],
                                'sedang_disewa'           => ['Sedang Disewa', 'badge-blue'],
                                'dikembalikan'            => ['Dikembalikan', 'badge-green'],
                                'terlambat'               => ['Terlambat', 'badge-red'],
                                'dibatalkan'              => ['Dibatalkan', 'badge-red'],
                                default                   => [$rental->status, 'badge-yellow'],
                            };
                        @endphp
                        <span class="status-badge {{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-block-label">ID Transaksi</div>
                    <div class="info-block-value" style="font-family: 'Space Mono', monospace; font-size: 0.75rem;">
                        {{ strtoupper(substr($rental->id, 0, 16)) }}...
                    </div>
                </div>
            </div>

            <!-- Detail Item/Paket -->
            <div class="section-title">Rincian Sewa</div>
            <table class="item-table">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Keterangan</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @if($rental->item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $rental->item->name }}</div>
                                <div class="item-sub">{{ $rental->item->category->name ?? '' }} • {{ $rental->item->call_code }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.75rem; color: rgba(44,30,51,0.6);">
                                    Size: {{ $rental->item->size_label ?? '—' }}<br>
                                    Durasi 3 hari
                                </div>
                            </td>
                            <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @elseif($rental->package)
                        <tr>
                            <td>
                                <div class="item-name">{{ $rental->package->name }}</div>
                                <div class="item-sub">{{ $rental->package->category->name ?? '' }} • Paket Lengkap</div>
                            </td>
                            <td>
                                <div style="font-size: 0.75rem; color: rgba(44,30,51,0.6);">
                                    {{ $rental->package->items->count() }} item termasuk<br>
                                    Durasi 3 hari
                                </div>
                            </td>
                            <td>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    @foreach($rental->addons as $addon)
                        <tr>
                            <td>
                                <div class="item-name">{{ $addon->service->name ?? $addon->service_id }}</div>
                                <div class="item-sub">Layanan Tambahan (Add-on)</div>
                            </td>
                            <td></td>
                            <td>Rp{{ number_format($addon->price_snapshot, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals">
                @php
                    $addonTotal = $rental->addons->sum('price_snapshot');
                    $basePrice = (float) $rental->total_price;
                    $grandTotal = $basePrice + $addonTotal + (float) $rental->fine_amount;
                @endphp

                <div class="total-row">
                    <span class="total-label">Harga Sewa</span>
                    <span class="total-value">Rp{{ number_format($basePrice, 0, ',', '.') }}</span>
                </div>

                @if($addonTotal > 0)
                    <div class="total-row">
                        <span class="total-label">Add-on Layanan</span>
                        <span class="total-value">Rp{{ number_format($addonTotal, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if((float)$rental->fine_amount > 0)
                    <div class="total-row">
                        <span class="total-label" style="color: var(--poppy-red);">Denda Keterlambatan</span>
                        <span class="total-value" style="color: var(--poppy-red);">+ Rp{{ number_format($rental->fine_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="total-row grand-total">
                    <span class="total-label">Total Pembayaran</span>
                    <span class="total-value">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Catatan -->
            @if($rental->payment_notes)
                <div class="section-title" style="margin-top: 1rem;">Catatan</div>
                <p style="font-size: 0.8rem; color: rgba(44,30,51,0.6); font-style: italic;">
                    {{ $rental->payment_notes }}
                </p>
            @endif

        </div>

        <!-- ── Footer ── -->
        <div class="invoice-footer">
            <div class="footer-note">
                Nota ini sah tanpa tanda tangan. Simpan sebagai bukti transaksi.<br>
                Terima kasih telah mempercayakan momen spesial Anda kepada WeddingKita.
            </div>
            <div class="footer-brand">WeddingKita</div>
        </div>

    </div>

</body>
</html>
