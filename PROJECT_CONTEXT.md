# PROJECT_CONTEXT.md — WeddingKita

> Sumber kebenaran untuk AI Agent lain yang bergabung di tengah project ini.
> Update file ini setiap kali ada progress signifikan.

---

## 1. Identitas Proyek

| Field | Value |
|---|---|
| **Nama** | WeddingKita |
| **Tipe** | Rental busana pengantin single-vendor |
| **Status** | 🟢 Sistem Pembayaran & Critical Logic Fixes Selesai — Siap Production Testing |
| **Terakhir diupdate** | 2026-07-27 |

---

## 2. Tech Stack (Final)

| Layer | Pilihan | Catatan |
|---|---|---|
| Backend | **Laravel 13.x** (PHP 8.4) | Laragon lokal; artisan via `C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe` |
| Database | **PostgreSQL** | pgAdmin; extension `btree_gist` aktif via migration |
| Frontend | **Blade + Tailwind CSS + Alpine.js** | Bukan React/Inertia |
| Auth | **Laravel Breeze** (Blade scaffold) | Session-based |
| Queue | Laravel Queue (database driver) | Untuk notifikasi & denda otomatis |

### ⚠️ Penting — PHP Execution di Laragon
```powershell
# SELALU gunakan PHP ini untuk artisan dan composer:
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan [command]
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe C:\laragon\bin\composer\composer.phar [command]

# PHP XAMPP (8.0.30) di PATH tidak kompatibel dengan Laravel 13
```

---

## 3. Sumber Kebenaran Desain & Aturan

| File | Isi |
|---|---|
| [`01-DESIGN.md`](file:///c:/laragon/www/weddingkita/01-DESIGN.md) | Token warna, tipografi, wireframe, signature element Label Gantung |
| [`02-PRD.md`](file:///c:/laragon/www/weddingkita/02-PRD.md) | Skema DB, user flow, MVP scope, fase pengembangan |
| [`03-RULES.md`](file:///c:/laragon/www/weddingkita/03-RULES.md) | Aturan wajib DB/query/backend/frontend/security/testing |

**Jangan improvisasi di luar ketiga file ini.**

---

## 4. Database

### Koneksi (.env)
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=weddingkita
DB_USERNAME=postgres
DB_PASSWORD=
```

### Tabel (urutan migration)

| Order | File Migration | Tabel |
|---|---|---|
| 0001_01_01_000000 | create_users_table | `users`, `password_reset_tokens`, `sessions` |
| 0001_01_01_000001 | create_cache_table | `cache`, `cache_locks` |
| 0001_01_01_000002 | create_jobs_table | `jobs`, `job_batches`, `failed_jobs` |
| 2026_07_20_000001 | enable_btree_gist_extension | *(extension only)* |
| 2026_07_20_000002 | create_categories_table | `categories` |
| 2026_07_20_000003 | create_items_table | `items` |
| 2026_07_20_000004 | create_services_table | `services` |
| 2026_07_20_000005 | create_packages_table | `packages` |
| 2026_07_20_000006 | create_package_items_table | `package_items` |
| 2026_07_20_000007 | create_package_services_table | `package_services` |
| 2026_07_20_000008 | create_rentals_table | `rentals` + **exclusion constraint** + fields pembayaran |
| 2026_07_20_000009 | create_rental_addons_table | `rental_addons` |
| 2026_07_20_000010 | create_fittings_table | `fittings` |
| 2026_07_20_000011 | create_reviews_and_favorites_tables | `reviews`, `favorites` |

### Constraint Kritis — Exclusion Constraint di `rentals`
```sql
-- Di migration 2026_07_20_000008
ALTER TABLE rentals
  ADD CONSTRAINT no_overlapping_rental
  EXCLUDE USING gist (
    item_id WITH =,
    daterange(pickup_at::date, return_due_at::date, '[]') WITH &&
  )
  WHERE (status IN ('dikonfirmasi', 'sedang_disewa'));
```
Error PostgreSQL: **23P01** (exclusion_violation) — ditangkap di `RentalService`.

### Keputusan dari PRD §11 (Pertanyaan Terbuka)
| Pertanyaan | Jawaban |
|---|---|
| Durasi sewa | Tidak bisa diperpanjang |
| Skema jaminan/deposit | Tidak ada skema jaminan (deposit_amount = 0) |
| Denda keterlambatan | **Rp 20.000/hari**, tidak dari deposit |
| Kapasitas fitting | **5 ruang paralel** per slot waktu |

---

## 5. Models (app/Models/)

| Model | File | Keterangan |
|---|---|---|
| `User` | [`User.php`](file:///c:/laragon/www/weddingkita/app/Models/User.php) | UUID PK, role (member/admin), `isAdmin()`, `isMember()` |
| `Category` | [`Category.php`](file:///c:/laragon/www/weddingkita/app/Models/Category.php) | No timestamps, name + slug |
| `Item` | [`Item.php`](file:///c:/laragon/www/weddingkita/app/Models/Item.php) | Unit fisik gaun/jas. Decimal casts. `isAvailable()` |
| `Service` | [`Service.php`](file:///c:/laragon/www/weddingkita/app/Models/Service.php) | Jasa MUA/dekorasi. No timestamps |
| `Package` | [`Package.php`](file:///c:/laragon/www/weddingkita/app/Models/Package.php) | Paket lengkap. Pivot ke items & services |
| `Rental` | [`Rental.php`](file:///c:/laragon/www/weddingkita/app/Models/Rental.php) | Transaksi sewa. Financial fields exclude dari fillable! |
| `RentalAddon` | [`RentalAddon.php`](file:///c:/laragon/www/weddingkita/app/Models/RentalAddon.php) | Snapshot harga add-on. No timestamps |
| `Fitting` | [`Fitting.php`](file:///c:/laragon/www/weddingkita/app/Models/Fitting.php) | Jadwal fitting. `SLOT_CAPACITY = 5` |
| `Review` | [`Review.php`](file:///c:/laragon/www/weddingkita/app/Models/Review.php) | Ulasan. Only created_at (no updated_at) |
| `Favorite` | [`Favorite.php`](file:///c:/laragon/www/weddingkita/app/Models/Favorite.php) | Item/Paket favorit member (UUID PK) |

---

## 6. Services (app/Services/)

| Service | File | Tanggung Jawab |
|---|---|---|
| `RentalService` | [`RentalService.php`](file:///c:/laragon/www/weddingkita/app/Services/RentalService.php) | Cek ketersediaan tanggal, buat rental (transaction), hitung denda, update status |
| `FittingService` | [`FittingService.php`](file:///c:/laragon/www/weddingkita/app/Services/FittingService.php) | Cek kapasitas slot (lockForUpdate), buat fitting (transaction), kalender fitting |
| `CatalogService` | [`CatalogService.php`](file:///c:/laragon/www/weddingkita/app/Services/CatalogService.php) | Filter katalog by tanggal/kategori, paginated listing, search ILIKE |
| `PaymentService` | [`PaymentService.php`](file:///c:/laragon/www/weddingkita/app/Services/PaymentService.php) | Upload bukti bayar, konfirmasi admin, izin pengambilan, hitung total harga |

---

## 7. Design Tokens

### Palet Warna (dari 01-DESIGN.md §2)
| Token | Hex | Fungsi |
|---|---|---|
| Petal Cream | `#FFF7EA` | Background utama (kanvas) |
| Plum Ink | `#2C1E33` | Teks, nav, garis struktural |
| Marigold | `#FFB627` | CTA, tombol primer, stempel "Tersedia" |
| Sky Ribbon | `#2F8FE0` | Kategori, link, navigasi sekunder |
| Blossom Pink | `#FF6F91` | Status "Fitting Terjadwal" |
| Meadow Green | `#3FAE68` | Status "Dikonfirmasi" / berhasil |
| Poppy Red | `#E4572E` | Warning, denda, error |

### Tipografi (dari 01-DESIGN.md §3)
| Peran | Font |
|---|---|
| Display | **Instrument Serif** (Google Fonts) |
| Body | **Plus Jakarta Sans** |
| Utility / kode | **Space Mono** |

### Signature Element
- **Label Gantung Interaktif** (garment-tag-card): swing -3deg, stempel status oval, hover buka strip 14 hari ketersediaan
- Animasi HANYA di elemen ini. Hormati `prefers-reduced-motion`.

---

## 8. Struktur Folder

```
app/
  Console/
    Commands/        # CheckOverdueRentals.php (cron denda otomatis)
  Http/
    Controllers/     # Thin controllers — hanya terima request + panggil Service:
                     #   AdminItemController.php (CRUD Item Admin)
                     #   AdminPackageController.php (CRUD Paket Admin)
                     #   CatalogController.php, RentalController.php, DashboardController.php
                     #   PaymentController.php (Upload bukti, cetak nota, konfirmasi)
    Middleware/      # Auth (CheckRole middleware)
    Requests/        # Form Request (validasi server-side):
                     #   StoreItemRequest.php, UpdateItemRequest.php
                     #   StorePackageRequest.php, UpdatePackageRequest.php
                     #   StoreRentalRequest.php, StoreFittingRequest.php
                     #   UpdateRentalStatusRequest.php, UpdateFittingStatusRequest.php
  Mail/              # Mailable classes:
                     #   RentalConfirmedMail.php  (antrian email konfirmasi sewa)
                     #   FittingScheduledMail.php (antrian email konfirmasi fitting)
  Models/            # Semua Eloquent model
  Policies/          # Authorization Policies (terdaftar di AppServiceProvider):
                     #   RentalPolicy.php
                     #   FittingPolicy.php
                     #   ItemPolicy.php
                     #   PackagePolicy.php
  Providers/
    AppServiceProvider.php  # Gate::policy() untuk semua policy
  Services/          # RentalService, FittingService, CatalogService, PaymentService
database/
  migrations/        # 14 migration files (lihat tabel di §4)
  seeders/           # Data dummy koleksi/kategori/jasa
resources/
  views/
    admin/           # Tampilan Manajemen Admin:
                     #   items/index.blade.php, items/create.blade.php, items/edit.blade.php
                     #   packages/index.blade.php, packages/create.blade.php, packages/edit.blade.php
    components/      # Blade components:
                     #   garment-tag-card.blade.php
                     #   stamp-badge.blade.php
                     #   date-availability-strip.blade.php
                     #   fitting-calendar.blade.php
    emails/          # Email templates (Blade):
                     #   rental_confirmed.blade.php
                     #   fitting_scheduled.blade.php
    layouts/         # app.blade.php, admin.blade.php, guest.blade.php
    pages/           # item-detail.blade.php, package-detail.blade.php
    rentals/         # invoice.blade.php
routes/
  web.php
```
---

## 9. Progress Fase Pengembangan

| Fase | Status | Keterangan |
|---|---|---|
| **Fase 1: Setup** | ✅ Selesai | Laravel 13 + PostgreSQL + btree_gist + Breeze terinstall, .env diupdate |
| **Fase 2: Backend Inti** | ✅ Selesai | Semua migration, semua model (UUID, relasi, casts), semua service |
| **Fase 3: Frontend** | ✅ Selesai | Tailwind config tokens, Blade layout, komponen Label Gantung, halaman koleksi/detail/dashboard |
| **Fase 4: Integrasi & Fitur Inti** | ✅ Selesai | Alur sewa, fitting scheduler, kalender fitting, riwayat transaksi |
| **Fase 5: Testing** | ✅ Selesai | Unit + Feature (AdminAccess, RentalController, PolicyAuthorization, dll) |
| **Fase 6: Priority 2 & CRUD Admin** | ✅ Selesai | Form Requests, Policies, Mailables, CRUD views untuk Item & Paket |
| **Fase 7: Pembayaran & Bug Fixes** | ✅ Selesai | Sistem konfirmasi pembayaran, cetak invoice, perbaikan logika overlap paket, tracking timeline UI/UX. Semua 53 test passed. |
| **Fase 8: Deploy** | 🔲 Belum | Production environment |

---

## 10. Aturan Wajib (Ringkasan dari 03-RULES.md)

1. **Selalu** Eloquent/Query Builder — **dilarang** raw string concatenation SQL
2. Controller harus **thin** — logika bisnis di `app/Services/`
3. Semua listing wajib **paginate()** — tidak pernah `->get()` seluruh tabel
4. Ketersediaan sewa: **dua lapis** — exclusion constraint DB + `RentalService`
5. Booking fitting: wajib `DB::transaction()` + `lockForUpdate()`
6. Validasi input: **selalu** lewat Form Request server-side
7. Ikuti **token warna & tipografi** dari `01-DESIGN.md` persis — jangan improvisasi
8. Komponen UI berulang → **Blade component** (bukan copy-paste HTML)
9. Data ketersediaan tanggal datang dari **backend** — Alpine.js hanya untuk interaksi
10. Jangan commit `.env` atau kredensial apapun

---

## 11. Cara Jalankan Lokal

```powershell
# 1. Pastikan PostgreSQL Laragon running + database 'weddingkita' sudah dibuat di pgAdmin
# 2. Jalankan migration
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan migrate

# 3. Seed data dummy (setelah seeder dibuat di Fase 3)
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan db:seed

# 4. Jalankan dev server
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan serve
npm run dev
```
