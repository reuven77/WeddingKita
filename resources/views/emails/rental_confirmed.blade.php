<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pemesanan Sewa Busana – WeddingKita</title>
    <style>
        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f0ea;
            color: #2C1E33;
            line-height: 1.7;
        }
        .wrapper {
            max-width: 600px;
            margin: 32px auto;
            background: #FFFDF8;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(44,30,51,0.1);
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #2C1E33 0%, #4A2E5A 100%);
            padding: 40px 36px 32px;
            text-align: center;
        }
        .header-badge {
            display: inline-block;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .header h1 {
            color: #FFB627;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            color: #FFF7EA;
            font-size: 13px;
            margin-top: 6px;
            opacity: 0.85;
        }
        /* Body */
        .body {
            padding: 36px;
        }
        .greeting {
            font-size: 18px;
            color: #2C1E33;
            margin-bottom: 16px;
        }
        .intro-text {
            font-size: 14px;
            color: #5A4A6A;
            margin-bottom: 28px;
        }
        /* Info Card */
        .info-card {
            background: linear-gradient(135deg, #FFF7EA 0%, #FEF3E2 100%);
            border: 1.5px solid #FFB627;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .info-card h2 {
            font-size: 13px;
            color: #FFB627;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 16px;
            font-family: 'Arial', sans-serif;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,182,39,0.2);
            font-size: 14px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .label {
            color: #7A6A8A;
            font-family: 'Arial', sans-serif;
            font-size: 13px;
        }
        .info-row .value {
            color: #2C1E33;
            font-weight: 600;
            text-align: right;
        }
        /* Status Badge */
        .status-badge {
            display: inline-block;
            background: #FFB627;
            color: #2C1E33;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-family: 'Arial', sans-serif;
        }
        /* Fitting Card */
        .fitting-card {
            background: #f0f8ff;
            border: 1.5px solid #87CEEB;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }
        .fitting-card h2 {
            font-size: 13px;
            color: #4682B4;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
            font-family: 'Arial', sans-serif;
        }
        /* Important Note */
        .note-box {
            background: #fff8f0;
            border-left: 4px solid #FFB627;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
            font-size: 13px;
            color: #6A4A2A;
        }
        .note-box strong { color: #2C1E33; }
        /* CTA */
        .cta-section { text-align: center; margin-bottom: 32px; }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #FFB627, #FF9500);
            color: #2C1E33;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 30px;
            font-family: 'Arial', sans-serif;
            letter-spacing: 0.3px;
        }
        /* Footer */
        .footer {
            background: #2C1E33;
            text-align: center;
            padding: 24px;
            color: #9A8AA8;
            font-size: 12px;
            font-family: 'Arial', sans-serif;
        }
        .footer strong { color: #FFB627; }
        .divider {
            border: none;
            border-top: 1px solid rgba(44,30,51,0.1);
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .wrapper { margin: 0; border-radius: 0; }
            .body { padding: 24px 20px; }
            .header { padding: 32px 20px 24px; }
            .info-row { flex-direction: column; gap: 2px; }
            .info-row .value { text-align: left; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="header">
            <h1>WeddingKita</h1>
            <p>Butik Busana Pengantin Premium · Konfirmasi Pemesanan</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Halo, <strong>{{ $rental->user->name ?? 'Calon Pengantin' }}</strong></p>
            <p class="intro-text">
                Selamat! Pemesanan sewa busana pengantin Anda telah berhasil kami terima.
                Tim butik WeddingKita akan segera meninjau pesanan Anda dan melakukan konfirmasi.
            </p>

            <!-- Detail Pesanan -->
            <div class="info-card">
                <h2>Detail Pesanan Sewa</h2>
                <div class="info-row">
                    <span class="label">Nomor Transaksi</span>
                    <span class="value">{{ strtoupper(substr($rental->id, 0, 8)) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Busana</span>
                    <span class="value">
                        @if($rental->item)
                            {{ $rental->item->name }} <small>({{ $rental->item->call_code }})</small>
                        @elseif($rental->package)
                            Paket: {{ $rental->package->name }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal Acara</span>
                    <span class="value">{{ \Carbon\Carbon::parse($rental->event_date)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Jadwal Pengambilan</span>
                    <span class="value">{{ \Carbon\Carbon::parse($rental->pickup_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="label">Batas Pengembalian</span>
                    <span class="value">{{ \Carbon\Carbon::parse($rental->return_due_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Biaya Sewa</span>
                    <span class="value">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status</span>
                    <span class="value"><span class="status-badge">Menunggu Fitting</span></span>
                </div>
            </div>

            <!-- Detail Fitting -->
            <div class="fitting-card">
                <h2>Jadwal Fitting</h2>
                <div class="info-row">
                    <span class="label">Tanggal Fitting</span>
                    <span class="value">{{ \Carbon\Carbon::parse($fitting->scheduled_at)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Jam</span>
                    <span class="value">{{ \Carbon\Carbon::parse($fitting->scheduled_at)->format('H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="label">Durasi</span>
                    <span class="value">{{ $fitting->duration_minutes }} menit</span>
                </div>
            </div>

            <!-- Catatan Penting -->
            <div class="note-box">
                <strong>Penting:</strong> Busana akan siap diambil pada tanggal <strong>H-2 sebelum acara</strong>.
                Harap kembalikan busana paling lambat <strong>H+1 setelah acara pukul 17:00 WIB</strong>.
                Keterlambatan pengembalian dikenakan denda <strong>Rp 20.000/hari</strong>.
            </div>

            <hr class="divider">

            <!-- CTA -->
            <div class="cta-section">
                <p style="font-size:13px; color:#7A6A8A; margin-bottom:16px;">
                    Cek status pemesanan Anda di dashboard member:
                </p>
                <a href="{{ url('/dashboard') }}" class="cta-button">
                    Lihat Dashboard Saya →
                </a>
            </div>

            <p style="font-size:13px; color:#9A8AA8; text-align:center;">
                Jika ada pertanyaan, hubungi kami melalui telepon/WhatsApp butik.<br>
                Terima kasih telah mempercayakan hari istimewa Anda kepada WeddingKita.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>WeddingKita</strong> · Butik Busana Pengantin Single-Vendor<br>
            Email ini dikirim secara otomatis, mohon tidak membalas email ini.<br>
            © {{ date('Y') }} WeddingKita. Hak cipta dilindungi.
        </div>
    </div>
</body>
</html>
