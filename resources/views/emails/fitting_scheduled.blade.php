<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Fitting Terkonfirmasi – WeddingKita</title>
    <style>
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
        .header {
            background: linear-gradient(135deg, #1B5E3B 0%, #2E7D52 100%);
            padding: 40px 36px 32px;
            text-align: center;
        }
        .header-badge {
            display: inline-block;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .header h1 {
            color: #90EE90;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            color: #E8F5E9;
            font-size: 13px;
            margin-top: 6px;
            opacity: 0.85;
        }
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
        .fitting-card {
            background: linear-gradient(135deg, #F0FFF4 0%, #E8F5E9 100%);
            border: 1.5px solid #4CAF50;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .fitting-card h2 {
            font-size: 13px;
            color: #2E7D52;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 16px;
            font-family: 'Arial', sans-serif;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(76,175,80,0.2);
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
        .time-highlight {
            background: #2E7D52;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            margin: 16px 0;
        }
        .time-highlight .time-big {
            font-size: 28px;
            font-weight: 700;
            font-family: 'Arial', sans-serif;
            letter-spacing: 2px;
        }
        .time-highlight .time-date {
            font-size: 13px;
            opacity: 0.85;
            margin-top: 4px;
        }
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
        .tips-list {
            list-style: none;
            margin: 0 0 24px 0;
            padding: 0;
        }
        .tips-list li {
            padding: 6px 0;
            font-size: 13px;
            color: #5A4A6A;
        }
        .tips-list li::before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: 700;
        }
        .cta-section { text-align: center; margin-bottom: 32px; }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4CAF50, #2E7D52);
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 30px;
            font-family: 'Arial', sans-serif;
        }
        .footer {
            background: #2C1E33;
            text-align: center;
            padding: 24px;
            color: #9A8AA8;
            font-size: 12px;
            font-family: 'Arial', sans-serif;
        }
        .footer strong { color: #90EE90; }
        @media only screen and (max-width: 600px) {
            .wrapper { margin: 0; border-radius: 0; }
            .body { padding: 24px 20px; }
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
            <p>Butik Busana Pengantin Premium · Konfirmasi Jadwal Fitting</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Halo, <strong>{{ $fitting->user->name ?? 'Calon Pengantin' }}</strong></p>
            <p class="intro-text">
                Jadwal fitting busana pengantin Anda telah berhasil dikonfirmasi.
                Kami tidak sabar bertemu Anda di butik WeddingKita!
            </p>

            <!-- Highlight Waktu -->
            <div class="time-highlight">
                <div class="time-big">{{ \Carbon\Carbon::parse($fitting->scheduled_at)->format('H:i') }} WIB</div>
                <div class="time-date">{{ \Carbon\Carbon::parse($fitting->scheduled_at)->translatedFormat('l, d F Y') }}</div>
            </div>

            <!-- Detail Fitting -->
            <div class="fitting-card">
                <h2>Detail Jadwal Fitting</h2>
                <div class="info-row">
                    <span class="label">ID Fitting</span>
                    <span class="value">{{ strtoupper(substr($fitting->id, 0, 8)) }}</span>
                </div>
                @if($fitting->item)
                <div class="info-row">
                    <span class="label">Busana yang Difitting</span>
                    <span class="value">{{ $fitting->item->name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Kode Item</span>
                    <span class="value">{{ $fitting->item->call_code }}</span>
                </div>
                @elseif($fitting->package)
                <div class="info-row">
                    <span class="label">Paket Busana</span>
                    <span class="value">{{ $fitting->package->name }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">Durasi Sesi</span>
                    <span class="value">{{ $fitting->duration_minutes }} menit</span>
                </div>
                <div class="info-row">
                    <span class="label">Status</span>
                    <span class="value" style="color: #2E7D52; font-weight: 700;">✓ Terjadwal</span>
                </div>
                @if($fitting->notes)
                <div class="info-row">
                    <span class="label">Catatan</span>
                    <span class="value">{{ $fitting->notes }}</span>
                </div>
                @endif
            </div>

            <!-- Tips Persiapan -->
            <p style="font-size:14px; font-weight:600; color:#2C1E33; margin-bottom:10px;">Persiapan sebelum datang fitting:</p>
            <ul class="tips-list">
                <li>Datang tepat waktu sesuai jadwal yang telah dipilih</li>
                <li>Kenakan pakaian dalam yang nyaman untuk memudahkan fitting</li>
                <li>Bawa referensi gaya/foto inspirasi yang Anda inginkan</li>
                <li>Catat nomor ID Fitting di atas untuk konfirmasi di butik</li>
            </ul>

            <!-- Catatan -->
            <div class="note-box">
                <strong>📍 Lokasi Butik:</strong> Silakan hubungi butik WeddingKita untuk konfirmasi alamat lengkap.
                Jika perlu reschedule, harap informasikan <strong>minimal 24 jam sebelumnya</strong>.
            </div>

            <!-- CTA -->
            <div class="cta-section">
                <a href="{{ url('/dashboard') }}" class="cta-button">
                    Lihat Dashboard Saya →
                </a>
            </div>

            <p style="font-size:13px; color:#9A8AA8; text-align:center;">
                Sampai jumpa di butik! Kami siap membantu Anda tampil sempurna di hari istimewa.
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
