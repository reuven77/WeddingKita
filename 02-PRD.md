# WeddingKita — Product Requirements Document (PRD)

## 1. Ringkasan

WeddingKita adalah aplikasi rental busana pengantin **single-vendor** (satu butik) yang menangani penyewaan **gaun, jas pria, aksesoris**, dan **paket lengkap** (busana + aksesoris + jasa MUA/dekorasi). Berbeda dari perpustakaan buku, unit yang disewakan (gaun/jas fisik) hanya bisa dipakai untuk **satu rentang tanggal acara pada satu waktu** — ketersediaan terikat tanggal, bukan sekadar hitungan stok. Dibangun di atas Laravel + PostgreSQL, dijalankan lokal via Laragon selama development.

## 2. Tujuan produk

- Mempercepat proses cari → cek ketersediaan **untuk tanggal acara tertentu** → booking fitting → sewa, tanpa perlu telepon/DM manual ke butik untuk cek jadwal.
- Mencatat seluruh transaksi penyewaan secara auditable (siapa sewa apa, tanggal acara, status, jaminan/deposit, denda).
- Mengelola **jadwal fitting** di butik (kapasitas ruang fitting terbatas per slot waktu) agar tidak dobel-booking.
- Memberi admin/pemilik butik dashboard untuk mengelola koleksi, jadwal fitting, dan transaksi tanpa akses langsung ke database.

## 3. Target pengguna

| Peran | Kebutuhan utama |
|---|---|
| **Member** (calon pengantin) | Cari paket by tanggal acara, cek ketersediaan, booking fitting, sewa, lihat riwayat & status jaminan/denda, beri ulasan |
| **Admin/Pemilik Butik** | CRUD koleksi & paket, kelola jadwal fitting, kelola transaksi & jaminan/denda, kelola user, lihat statistik |

## 4. Tech stack (final)

| Layer | Pilihan | Catatan |
|---|---|---|
| Backend | **Laravel 11** (PHP 8.2+) | via Laragon lokal |
| Database | **PostgreSQL** | dikelola lewat **pgAdmin**; memanfaatkan tipe `daterange` + exclusion constraint (lihat `03-RULES.md` §1) |
| Frontend | **Blade** + **Tailwind CSS** + **Alpine.js** | bukan React — lihat `01-DESIGN.md` §7 untuk alasan |
| File storage (foto koleksi, kontrak sewa) | Lokal `storage/app/public` saat dev → migrasi ke S3-compatible (mis. Cloudflare R2) saat production | jangan simpan file besar di repo git |
| Auth | Laravel Breeze/Fortify (session-based, bukan token API kecuali nanti butuh mobile app) | sesuai konteks web-only saat ini |
| Queue/Scheduler | Laravel Queue + Scheduler bawaan (hitung denda otomatis harian, reminder fitting H-1) | |
| Search | PostgreSQL full-text search (`tsvector`) untuk MVP; Meilisearch opsional fase lanjut | hindari overengineering di awal |

## 5. Skema database (PostgreSQL)

Disesuaikan dari materi Database MKG (normalisasi 3NF, transaction, constraint) — dengan satu penyesuaian penting dibanding proyek e-library: **ketersediaan berbasis rentang tanggal**, bukan angka stok.

```
users
  id (uuid, pk)
  name, email (unique), password_hash
  role (enum: member, admin)
  phone
  created_at, updated_at

categories
  id (uuid, pk)
  name, slug (unique)          -- Gaun Klasik, Gaun Modern, Jas Pria, Aksesoris, Paket Lengkap

items
  id (uuid, pk)                -- SATU unit fisik (satu gaun spesifik, satu jas spesifik, dst)
  category_id (fk -> categories)
  name, description
  call_code (varchar)          -- kode koleksi, ditampilkan di UI, mis. "WK-0142"
  size_label (varchar)         -- mis. "M / Lingkar dada 88cm"
  cover_image_path
  base_price (numeric(12,2))   -- harga sewa dasar per paket durasi standar
  deposit_amount (numeric(12,2)) -- jaminan/deposit wajib
  status (enum: aktif, maintenance, nonaktif)
  created_at, updated_at

services
  id (uuid, pk)
  name                          -- "Jasa MUA", "Dekorasi Mini", dll
  description
  price (numeric(12,2))
  is_active (boolean)

packages
  id (uuid, pk)
  name, slug
  description
  category_id (fk -> categories)  -- biasanya "Paket Lengkap"
  cover_image_path
  base_price (numeric(12,2))
  deposit_amount (numeric(12,2))
  created_at, updated_at

package_items                  -- item fisik apa saja yang termasuk 1 paket
  package_id (fk -> packages)
  item_id (fk -> items)
  primary key (package_id, item_id)

package_services                -- jasa opsional yang bisa ditambahkan ke paket
  package_id (fk -> packages)
  service_id (fk -> services)
  is_default (boolean)          -- termasuk otomatis atau opsional-tambah-biaya
  primary key (package_id, service_id)

rentals   -- transaksi sewa (setara "loans" di proyek e-library, tapi berbasis rentang tanggal)
  id (uuid, pk)
  user_id (fk -> users)
  item_id (fk -> items, nullable)     -- isi salah satu: item_id ATAU package_id
  package_id (fk -> packages, nullable)
  event_date (date)                    -- tanggal acara utama
  pickup_at, return_due_at (timestamptz)
  returned_at (timestamptz, nullable)
  status (enum: menunggu_fitting, dikonfirmasi, sedang_disewa, dikembalikan, terlambat, dibatalkan)
  deposit_status (enum: ditahan, dikembalikan, dipotong_denda)
  fine_amount (numeric(12,2), default 0)
  total_price (numeric(12,2))
  created_at, updated_at
  CHECK (item_id IS NOT NULL OR package_id IS NOT NULL)

rental_addons                   -- jasa tambahan yang dipilih untuk 1 transaksi sewa
  id (uuid, pk)
  rental_id (fk -> rentals)
  service_id (fk -> services)
  price_at_booking (numeric(12,2))

fittings                        -- jadwal coba baju di butik
  id (uuid, pk)
  user_id (fk -> users)
  rental_id (fk -> rentals, nullable)   -- boleh booking fitting sebelum deal final
  item_id (fk -> items, nullable)
  package_id (fk -> packages, nullable)
  scheduled_at (timestamptz)
  duration_minutes (int, default 60)
  status (enum: terjadwal, selesai, batal, tidak_hadir)
  notes (text, nullable)                -- catatan ukuran/permintaan khusus
  created_at, updated_at

reviews
  id (uuid, pk)
  user_id (fk -> users), package_id (fk -> packages, nullable), item_id (fk -> items, nullable)
  rating (smallint, check 1-5)
  comment (text)
  created_at

favorites
  user_id (fk -> users), package_id (fk -> packages), primary key (user_id, package_id)
```

Catatan kepatuhan aturan (lihat `03-RULES.md` untuk detail lengkap):
- Uang (`base_price`, `deposit_amount`, `fine_amount`, `total_price`, `price`) pakai `NUMERIC`, **bukan** `FLOAT`.
- Semua timestamp `TIMESTAMPTZ`, disimpan UTC.
- FK & constraint **tidak boleh dihapus** demi kecepatan.
- **Wajib**: exclusion constraint berbasis `daterange` di `rentals` per `item_id` agar satu gaun/jas fisik tidak bisa dipesan untuk dua rentang tanggal acara yang tumpang tindih — ini pengganti pola "cek `stock > 0`" di proyek e-library. Detail SQL di `03-RULES.md` §1.
- Index eksplisit di `rentals.user_id`, `rentals.item_id`, `rentals.package_id`, `rentals.status`, `rentals.event_date`, `fittings.scheduled_at`.

## 6. Struktur folder Laravel

```
app/
  Http/
    Controllers/        # thin — hanya terima request, panggil service, return view/response
    Middleware/          # auth, role check (admin)
    Requests/             # Form Request untuk validasi server-side
  Models/
  Services/              # logika bisnis: RentalService (cek ketersediaan tanggal, hitung denda),
                          # FittingService (cek kapasitas slot), CatalogService
  Policies/               # otorisasi per-resource (mis. hanya admin bisa hapus item/paket)
database/
  migrations/
  seeders/                # data dummy koleksi/kategori/jasa untuk dev
resources/
  views/
    components/            # Blade components: garment-tag-card.blade.php, stamp-badge.blade.php,
                            # date-availability-strip.blade.php, fitting-calendar.blade.php, dll
    layouts/
    pages/
  css/app.css               # entry Tailwind
routes/
  web.php
  admin.php                  # di-prefix /admin, middleware role:admin
```

## 7. Alur pengguna (User Flow)

### Member
1. Registrasi/Login (Laravel Breeze).
2. Di beranda, isi **tanggal acara** → jelajahi kategori/paket yang tersedia untuk tanggal tersebut.
3. Buka detail paket/item → lihat status ketersediaan (stempel + strip 14 hari) untuk tanggal yang diisi.
4. Kalau tersedia: **Jadwalkan Fitting** → pilih slot waktu (sesuai kapasitas ruang fitting butik, dicek di dalam **DB transaction** agar dua orang tidak mendapat slot sama secara bersamaan).
5. Setelah fitting (atau langsung, kalau ukuran sudah pasti): konfirmasi sewa → sistem cek **tidak ada tumpang tindih tanggal** untuk item terkait via exclusion constraint → buat `rentals` row berstatus `dikonfirmasi`, hitung `deposit_amount` & `total_price` (termasuk `rental_addons` jasa MUA/dekorasi kalau dipilih).
6. Pickup & pengembalian: admin update status (`sedang_disewa` → `dikembalikan`), hitung denda otomatis kalau lewat `return_due_at`, update `deposit_status`.
7. Beri ulasan setelah status `dikembalikan`.

### Admin/Pemilik Butik
1. Login → dashboard (`/admin`): total paket/item, sedang disewa, jadwal fitting hari ini, total denda outstanding.
2. CRUD item, paket, kategori, jasa tambahan (upload foto, atur harga & deposit).
3. Kelola kalender fitting: lihat slot terisi/kosong, konfirmasi/batalkan janji.
4. Kelola penyewaan: konfirmasi pickup & pengembalian, hitung/reset denda, catat status jaminan.
5. Kelola user: blokir user yang menunggak denda.

## 8. Fitur — MVP vs Fase Lanjut

**MVP (wajib jalan dulu):**
- Auth + role (member/admin)
- CRUD item, paket, kategori, jasa tambahan (admin)
- Cari & filter koleksi berdasarkan **tanggal acara**
- Booking fitting dengan validasi kapasitas slot atomik
- Sewa item/paket dengan validasi non-tumpang-tindih tanggal (exclusion constraint)
- Perhitungan jaminan/deposit & total harga (termasuk add-on jasa)
- Dashboard admin dasar (ringkasan + kalender fitting)

**Fase lanjut:**
- Denda otomatis via `php artisan schedule` harian (job cek `return_due_at < now() AND status = 'sedang_disewa'`)
- Reminder H-1 fitting via email/notifikasi
- Ulasan & rating
- Favorit/bookmark
- Rekomendasi sederhana (paket serupa berdasarkan kategori)
- Full-text search upgrade ke Meilisearch
- Pembayaran online (payment gateway) untuk DP/jaminan

## 9. Non-functional requirements

- **Keamanan**: parameterized query (Eloquent/query builder — dilarang string concatenation raw SQL), password hash Argon2id/bcrypt (default Laravel sudah bcrypt — jangan diturunkan), CSRF protection bawaan Laravel tetap aktif, role check di middleware **dan** Policy (defense in depth).
- **Performa**: query list koleksi wajib pagination (jangan `SELECT *` tanpa limit), index sesuai §5.
- **Observability**: gunakan Laravel log channel + query log di staging untuk cek N+1 query.
- **Backup**: dump PostgreSQL terjadwal (pg_dump) minimal harian saat sudah production, uji restore berkala — penting karena ada data finansial (jaminan/denda).

## 10. Fase pengembangan (disesuaikan timeline SDLC)

| Fase | Isi |
|---|---|
| 1. Setup | Laragon + Laravel + PostgreSQL connect, aktifkan extension `btree_gist` untuk exclusion constraint, migration awal, repo Git, `.env` per environment |
| 2. Backend inti | Auth, model + migration semua tabel §5, CRUD koleksi (admin), Service layer penyewaan & fitting |
| 3. Frontend | Blade layout + Tailwind sesuai `01-DESIGN.md`, komponen label gantung, halaman koleksi/detail/dashboard |
| 4. Integrasi & fitur inti | Alur cek-tanggal → fitting → sewa, validasi non-tumpang-tindih, kalender fitting, halaman riwayat |
| 5. Testing | Unit test `RentalService` (kasus tanggal bentrok, dua request bersamaan booking tanggal sama), `FittingService` (slot penuh), feature test route utama |
| 6. Deploy | Siapkan environment production (VPS/hosting PHP + managed PostgreSQL), migrasi storage file ke cloud |

## 11. Pertanyaan terbuka (perlu kamu putuskan, tandai di Cursor jika belum jelas)

- Berapa lama durasi sewa standar per paket (mis. 3 hari sebelum acara sampai 1 hari sesudah)? Apakah bisa diperpanjang? Jawaban: Tidak usah bisa diperpanjang
- Skema jaminan/deposit: dikembalikan penuh kalau tidak ada kerusakan, atau ada potongan tetap (biaya cuci/perawatan)? Jawaban: tidak usah ada skema jaminan
- Skema denda: nominal per hari terlambat berapa? Apakah dipotong langsung dari deposit? Jawaban: denda 20K/ hari, tidak dari deposit
- Kapasitas ruang fitting: berapa ruang/slot paralel yang tersedia per jam di butik? Jawaban: 5 saja
