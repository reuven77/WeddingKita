# WeddingKita — Design Brief & Token System

> Dokumen ini adalah hasil proses *brainstorm → token system → kritik diri* sesuai frontend-design playbook. Tujuannya: memberi Cursor (dan kamu) satu sumber kebenaran visual, bukan sekadar "pakai warna bagus".

## 0. Ground the subject

- **Produk:** WeddingKita — rental butik pengantin *single-vendor*. Menyewakan gaun, jas pria, aksesoris, dan **paket lengkap** (baju + aksesoris + jasa MUA/dekorasi).
- **Audiens:** calon pengantin yang sedang membandingkan paket sewa untuk **satu tanggal acara spesifik** — bukan belanja "kapan saja". Mereka datang dengan tanggal di kepala, bukan kategori.
- **Satu pekerjaan utama halaman beranda:** membantu orang **menemukan paket yang tepat dan langsung tahu apakah tersedia untuk tanggal acara mereka** — lalu mengunci jadwal fitting di butik.
- Ini bukan marketplace multi-vendor — satu butik, satu koleksi terkurasi. Identitas: **WeddingKita**, nada butik-couture yang hangat dan personal, bukan katalog e-commerce masif.

## 1. Kenapa tidak pakai default AI-look

Empat default yang saya hindari secara sadar:
1. Krem hangat + serif kontras + **satu** aksen terracotta redup (default AI umum).
2. Hitam pekat + satu aksen neon (default AI umum).
3. Broadsheet hairline serba tajam (default AI umum).
4. **Blush pink pudar + gold foil + font script kursif** — default *industri bridal itu sendiri*. Hampir semua situs rental gaun pengantin di Indonesia jatuh ke kombinasi ini.

**Revisi arah (atas permintaan eksplisit): cerah & ceria**, dengan `pionir.ugm.ac.id/2026` sebagai referensi rasa. Situs itu punya kanvas krem terang (`#fffdf3`), tapi yang membuatnya terasa hidup bukan krem-nya — melainkan **banyak aksen jenuh sekaligus** (oranye, kuning, biru, pink) dipakai berdampingan lewat ilustrasi/ornamen, bukan satu aksen redup tunggal. Itu justru kebalikan dari default #1 di atas (yang cirinya *satu* aksen pucat). WeddingKita mengadopsi prinsip itu: kanvas terang + beberapa warna jenuh yang masing-masing punya fungsi jelas, bukan dekorasi acak — supaya "cerah" tidak jatuh jadi "ramai tanpa arah".

Dunia yang tetap dipegang sebagai sumber kejujuran visual: **atelier penjahit** — pita ukur, jarum pentul, kain contoh, label gantung baju, buku jadwal fitting. Yang berubah dari revisi sebelumnya hanya *suhu* palet (dari redup-beludru ke terang-jenuh), bukan sumber materialnya — supaya tetap terasa milik dunia pernikahan/butik, bukan berubah jadi situs event kampus.

Satu keputusan sadar untuk menjaga "tetap bagus" (bukan norak): tetap ada **satu warna gelap jangkar** (Plum Ink) untuk teks & elemen struktural (nav, garis, label mono). Tanpa jangkar gelap ini, kanvas terang + banyak warna jenuh akan terasa seperti poster anak-anak, bukan butik pengantin profesional. Ini justru pelajaran dari `pionir.ugm.ac.id`: warna-warni yang playful tetap dibangun di atas struktur tipografi yang rapi dan disiplin.

## 2. Token — Warna

| Nama | Hex | Pemakaian |
|---|---|---|
| Petal Cream | `#FFF7EA` | Background utama — kanvas terang hangat, senada dengan referensi (`#fffdf3`) tapi diberi sedikit rona petal/kain, bukan kertas polos |
| Plum Ink | `#2C1E33` | Warna jangkar — nav, teks judul, teks body utama, garis/outline pada elemen berwarna (ini yang mencegah palet cerah terasa kekanakan) |
| Marigold | `#FFB627` | Aksen utama/CTA — tombol primer, stempel status "Tersedia", garis bawah link aktif (peran setara Brass Pin di versi sebelumnya, tapi jauh lebih jenuh & cerah) |
| Sky Ribbon | `#2F8FE0` | Aksen sekunder — kategori (mis. "Gaun Modern"), link, elemen navigasi sekunder |
| Blossom Pink | `#FF6F91` | Aksen fungsional — status "Fitting Terjadwal" (versi cerah dari Dusty Rose sebelumnya; tetap dipakai sesempit mungkin — sebagai penanda status, bukan tema warna keseluruhan halaman) |
| Meadow Green | `#3FAE68` | Aksen fungsional — status "Dikonfirmasi"/berhasil |
| Poppy Red | `#E4572E` | Aksen peringatan — status "Terlambat"/denda/kerusakan, error state |

Aturan pemakaian: **Petal Cream + Plum Ink** tetap jadi tulang punggung tipografi & layout (±70% halaman). Marigold/Sky Ribbon/Blossom Pink/Meadow Green/Poppy Red muncul sebagai **blok warna solid berukuran kecil-menengah** (stempel, badge, garis kategori, ikon status, tombol) — meniru cara beberapa pita warna berbeda dipakai berdampingan di etalase butik untuk menandai kategori/ukuran, bukan sebagai gradasi dekoratif di background besar. Jangan pernah menumpuk 2+ warna aksen jenuh berdekatan tanpa Petal Cream/Plum Ink sebagai jeda visual di antaranya.

## 3. Token — Tipografi

| Peran | Font | Alasan |
|---|---|---|
| Display (judul besar) | **Instrument Serif** (Google Fonts) | Serif modern dengan lekukan hangat & italic yang ekspresif — terasa ceria dan personal tanpa jatuh ke font script kursif klise pernikahan (Cormorant, Great Vibes), dan cukup kontras untuk berdampingan dengan palet warna jenuh tanpa saling berebut perhatian |
| Body | **Plus Jakarta Sans** | Sans rounded, ramah, sangat terbaca; dirancang di Jakarta — kecocokan tematik untuk produk Indonesia, terasa hangat tanpa kaku-institusional |
| Utility / data (kode paket, tanggal acara, kode booking, ukuran) | **Space Mono** | Tetap dipertahankan dari versi sebelumnya — jadi elemen "disiplin" yang menahan kesan playful supaya tidak berlebihan; dipakai untuk kode koleksi, rentang tanggal sewa, ukuran (cm), kode booking |

Skala tipe: Display 60/42/30px (desktop/tablet/mobile), Body 16px/1.6, Utility 13px tracking +0.02em uppercase untuk label kecil.

Catatan: **Instrument Serif** dipakai *dengan hemat* — hanya untuk headline dan judul paket. Untuk teks penjelasan, turun ke Plus Jakarta Sans. Warna teks display & body tetap **Plum Ink**, bukan warna aksen jenuh — supaya tetap terbaca nyaman di atas Petal Cream maupun di dalam blok warna aksen (dengan varian teks putih/Petal Cream saat di atas blok gelap/jenuh).

## 4. Layout concept

Struktur yang dipakai: **eyebrow label = kode koleksi asli** (mis. `WK-0142 · GAUN A-LINE`), bukan angka urut dekoratif — sama seperti nomor panggil di rak butik nyata, bukan hiasan.

Karena unit inti bisnis ini adalah **satu gaun/jas fisik yang hanya bisa dipakai untuk satu tanggal acara pada satu waktu** (bukan stok massal seperti buku), pencarian di beranda diarahkan oleh **tanggal acara**, bukan kata kunci semata.

### Wireframe — Beranda
```
┌─────────────────────────────────────────────┐
│ WeddingKita     Koleksi  Jadwal Saya  Masuk  │  ← nav, Petal Cream bg,
│                                               │    teks Plum Ink, garis
│                                               │    bawah aktif Marigold
├─────────────────────────────────────────────┤
│  WK · CEK TANGGAL ACARA (eyebrow, mono)      │
│  Temukan gaun untuk hari itu.    [Instrument │
│  Pilih tanggal acara, kami tunjukkan yang    │  Serif, large display,
│  tersedia — lalu kunci jadwal fitting.       │  Plum Ink di atas Petal
│  [ Tanggal Acara: __/__/____ ]  [ Cari ]     │  Cream, tombol Cari =
│                                               │  Marigold solid]
├─────────────────────────────────────────────┤
│  Etalase Butik — kategori sbg "pita kain"    │
│  ▌Gaun Klasik ▌Gaun Modern ▌Jas Pria         │  ← blok warna solid
│  ▌Aksesoris  ▌Paket Lengkap                  │    bergantian: Sky Ribbon/
│                                               │    Meadow Green/Marigold/
│                                               │    Blossom Pink, tiap
│                                               │    kategori 1 warna tetap
├─────────────────────────────────────────────┤
│  Label Gantung Koleksi (grid 4 kolom)        │  ← signature element
│  [tag][tag][tag][tag]                        │
└─────────────────────────────────────────────┘
```

### Wireframe — Detail Paket
```
┌───────────────────┬─────────────────────────┐
│  [foto gaun/paket] │ WK-0142 · GAUN A-LINE   │
│  (rasio label      │  (mono)                 │
│   gantung, sudut   │ Nama Paket Besar         │
│   atas ada lubang  │ Termasuk: Gaun + Veil +  │
│   pons + tali)     │ Sepatu + MUA (opsional)  │
│                    │ Status: [Stempel: TERSEDIA│
│                    │  untuk 12–14 Okt 2026]   │
│                    │ [ Pilih Tanggal Acara ]  │
│                    │ [ Jadwalkan Fitting ]     │
│                    │ Detail bahan & ukuran...  │
└───────────────────┴─────────────────────────┘
```

### Wireframe — Dashboard Admin
```
┌─────────────────────────────────────────────┐
│ Ringkasan: Total Paket | Sedang Disewa |     │  ← angka besar mono
│ Fitting Hari Ini | Denda Outstanding         │
├─────────────────────────────────────────────┤
│ Kalender Fitting (slot per hari, kapasitas   │
│ ruang fitting ditandai warna token)          │
├─────────────────────────────────────────────┤
│ Tabel Penyewaan (status berwarna sesuai      │
│ token: Sage=dikonfirmasi, DustyRose=fitting, │
│ Rust=terlambat, Brass=tersedia/selesai)      │
└─────────────────────────────────────────────┘
```

## 5. Signature element — "Label Gantung Interaktif"

Satu elemen yang harus diingat orang dari WeddingKita: setiap paket ditampilkan sebagai **label gantung baju digital** (swing tag) —

- bagian atas label ada ilustrasi lubang pons kecil + tali tipis, label sedikit rotasi seolah tergantung di gantungan (-3deg, berubah acak tipis per kartu supaya terasa fisik bukan seragam sempurna),
- pojok kanan atas: kode koleksi dalam `Space Mono`, seperti kode yang diketik mesin tik di label butik lama,
- status ketersediaan tampil sebagai **stempel tinta** (bentuk oval tipis, warna Marigold/Meadow Green/Blossom Pink/Poppy Red dengan outline tipis Plum Ink supaya stempel warna cerah tetap terbaca jelas di atas Petal Cream, rotasi -4deg) — bukan badge pill generik,
- saat hover/tap, label **berayun tipis** (subtle swing rotation, bukan flip penuh) dan membuka **strip 14 hari ke depan** berbentuk titik-titik kecil berwarna status — ini fitur fungsional inti karena ketersediaan di bisnis ini terikat tanggal, bukan sekadar ya/tidak,
- di bawah strip tanggal ada CTA kecil "Cek Tanggal Acara Saya" yang membawa tanggal yang sudah diisi di pencarian beranda (jika ada) langsung ke kalkulasi ketersediaan paket ini.

Ini satu-satunya tempat animasi/swing dipakai secara sengaja. Sisanya statis dan tenang — kalau di-overuse di semua elemen, langsung terasa "AI-generated". (Catatan kritik-diri: awalnya elemen ini dirancang sebagai flip-card seperti pola katalog umum — direvisi jadi "swing + strip tanggal" karena flip card tidak menyampaikan informasi inti bisnis ini, yaitu *ketersediaan per rentang tanggal*, bukan sekadar sinopsis di sisi belakang.)

## 6. Aksesibilitas & motion

- Kontras teks Plum Ink di atas Petal Cream: AA+ terpenuhi dengan baik (gelap pekat di atas terang).
- **Wajib dicek manual**: teks putih/Petal Cream di atas Marigold, Blossom Pink, dan Meadow Green — warna-warna jenuh terang ini punya kontras lebih tipis dibanding Plum Velvet versi sebelumnya. Kalau kontras teks di dalam blok warna tidak lolos AA, gunakan teks **Plum Ink** (bukan putih) di atas blok warna terang tersebut, dan simpan teks putih hanya untuk blok Sky Ribbon/Poppy Red yang lebih gelap.
- Semua interaksi keyboard-focusable, focus ring pakai Marigold 2px dengan outline Plum Ink tipis di sekelilingnya agar tetap terlihat di atas latar apapun.
- `prefers-reduced-motion`: matikan swing animation, ganti jadi expand-inline (strip tanggal langsung terbuka tanpa rotasi).
- Status warna (Marigold/Meadow Green/Blossom Pink/Poppy Red) **selalu disertai teks/ikon dan outline Plum Ink**, tidak mengandalkan warna saja (untuk buta warna) — makin penting sekarang karena palet lebih jenuh dan ada 4 status berbeda.
- Blossom Pink dipakai secara sangat terbatas (hanya status "Fitting Terjadwal") — walau sekarang cerah, tetap dijaga agar tidak menyeret keseluruhan halaman kembali ke nuansa "situs wedding generik", karena yang membedakan WeddingKita adalah *kombinasi* warna, bukan dominasi satu warna pink.

## 7. Soal 21st.dev & godly.website

- **godly.website**: dipakai sebagai arah rasa (editorial fashion/couture, tenang, typography-led, jauh dari template "wedding vendor SaaS" generik) — bukan untuk dicontek 1:1. Terapkan prinsip: hierarki tipografi kuat (Italiana dipakai hemat), whitespace disiplin ala look-book butik, satu momen interaksi yang diorkestrasi (di sini: swing label + strip tanggal).
- **21st.dev**: karena berbasis React/Tailwind/Radix dan proyek ini Laravel Blade, gunakan sebagai referensi **struktur markup & pola Tailwind class** (khususnya untuk komponen date-picker/calendar strip dan card hover-state), lalu porting manual ke Blade component (`resources/views/components/`) + Alpine.js untuk interaktivitas (swing label, date-picker fitting, modal booking) — bukan `npx shadcn add` langsung. Detail teknisnya ada di `03-RULES.md`.
