# WeddingKita — Development Rules

> Ini adalah aturan wajib untuk siapapun (termasuk Cursor/AI) yang menulis kode di proyek ini. Disarikan dari materi Database MKG & PSI, diadaptasi khusus untuk Laravel + PostgreSQL dengan model bisnis **rental berbasis rentang tanggal**. Simpan file ini di root project; kalau pakai Cursor Rules native, salin isi bagian bertanda `[CURSOR RULE]` ke `.cursor/rules/weddingkita.mdc`.

## 1. Database & Migration

- **Boleh**: semua tabel wajib punya migration file, tidak ada perubahan schema manual lewat pgAdmin di environment yang di-share tim.
- **Boleh**: normalisasi hingga 3NF untuk tabel transaksional (`rentals`, `fittings`, `items`, dst). Denormalisasi hanya kalau ada bukti masalah performa nyata, bukan asumsi.
- **Boleh**: gunakan tipe data tepat — `NUMERIC` untuk uang (`base_price`, `deposit_amount`, `fine_amount`, `total_price`), `TIMESTAMPTZ` untuk semua waktu, `UUID` untuk primary key, `DATE` untuk `event_date`.
- **Wajib**: aktifkan extension `btree_gist` (`CREATE EXTENSION IF NOT EXISTS btree_gist;`) di migration paling awal — dibutuhkan untuk exclusion constraint di bawah.
- **Wajib**: cegah dua penyewaan tumpang tindih tanggal untuk item fisik yang sama pakai **PostgreSQL exclusion constraint**, bukan cuma validasi di level aplikasi:
  ```sql
  ALTER TABLE rentals
    ADD CONSTRAINT no_overlapping_rental
    EXCLUDE USING gist (
      item_id WITH =,
      daterange(pickup_at::date, return_due_at::date, '[]') WITH &&
    )
    WHERE (status IN ('dikonfirmasi', 'sedang_disewa'));
  ```
  Ini pengganti pola "cek `stock > 0`" di sistem stok biasa — di sini constraint di level database jadi garis pertahanan terakhir, validasi di Service layer tetap wajib ada juga (defense in depth, bukan pengganti).
- **Tidak boleh**: menghapus foreign key/constraint (termasuk exclusion constraint di atas) demi "biar gampang seed data".
- **Tidak boleh**: kolom `VARCHAR(255)` untuk semua teks tanpa mikir — sesuaikan panjang wajar (mis. `name varchar(300)`, `description text`).
- **Wajib**: index eksplisit di setiap kolom FK dan kolom yang sering di-`WHERE`/`ORDER BY` (lihat `02-PRD.md` §5), termasuk `rentals.event_date` dan `fittings.scheduled_at`.

## 2. Query & Eloquent

- **Wajib**: semua akses data lewat Eloquent ORM atau Query Builder Laravel — **dilarang keras** raw string concatenation ke SQL (`DB::raw("... $variable")` dengan variabel user-input langsung).
- Kalau terpaksa butuh raw query (mis. query `daterange`/`&&` yang belum didukung native oleh Eloquent), wajib pakai parameter binding: `DB::select('... where id = ?', [$id])`.
- **Tidak boleh** `SELECT *` di query production — pilih kolom eksplisit, apalagi untuk listing koleksi yang bisa banyak baris.
- **Wajib** pagination di semua listing (`->paginate()`), tidak pernah `->get()` semua baris tabel `items`/`packages`/`rentals` tanpa batas.
- Operasi penyewaan (cek tumpang tindih tanggal → buat record `rentals`) **wajib** dibungkus `DB::transaction()`. Karena validasi utama adalah exclusion constraint di database, tangkap exception `QueryException` (kode `23P01` — exclusion violation) di Service layer dan ubah jadi pesan error yang jelas ke user ("Tanggal ini sudah dipesan untuk item tersebut"), **jangan** tampilkan pesan SQL mentah.
- Operasi booking fitting (cek kapasitas slot → buat record `fittings`) **wajib** dibungkus `DB::transaction()` dengan `lockForUpdate()` saat menghitung jumlah fitting yang sudah terjadwal di slot waktu yang sama, untuk mencegah dua user mendapat slot terakhir bersamaan.

## 3. Backend / Controller / Service

- Controller **thin**: hanya terima Request, panggil Service/Model, kembalikan response. Logika bisnis (cek ketersediaan tanggal, hitung denda, hitung total harga + add-on, cek kapasitas fitting) hidup di `app/Services/` (`RentalService`, `FittingService`, `CatalogService`), bukan di Controller.
- Validasi input **selalu** server-side lewat Laravel Form Request class — jangan hanya andalkan validasi client-side/JS (termasuk validasi tanggal acara harus di masa depan, `event_date >= today`).
- **Tidak boleh** mengembalikan stack trace atau pesan error SQL mentah ke user — gunakan Laravel exception handler, tampilkan pesan generik ke user, log detail ke server.
- Role check admin **di dua tempat**: middleware route (`->middleware('role:admin')`) dan Policy (`ItemPolicy`, `PackagePolicy`, `RentalPolicy`) — bukan cuma cek `if ($user->role === 'admin')` tersebar di view.

## 4. Keamanan

- Password: pakai hashing bawaan Laravel (bcrypt/Argon2id) — **jangan pernah** custom hashing sendiri atau simpan plaintext.
- Session: gunakan session cookie HttpOnly bawaan Laravel — jangan simpan token sensitif di `localStorage`.
- CSRF protection Laravel **tidak boleh dimatikan** di form manapun kecuali endpoint API publik yang memang stateless dan sudah dilindungi cara lain.
- Rate limiting wajib di route login dan endpoint pencarian publik (`throttle` middleware) untuk cegah brute force/abuse.
- Environment secret (`DB_PASSWORD`, dsb) **hanya** di `.env`, tidak pernah di-commit — pastikan `.env` ada di `.gitignore` sejak commit pertama.
- Data PII (email, nama, nomor telepon) tidak boleh dipakai sebagai data dummy di environment non-prod tanpa masking — pakai Faker untuk seeder.
- Data finansial (`deposit_amount`, `fine_amount`, `total_price`) hanya boleh diubah lewat Service layer yang tercatat log-nya (audit trail) — jangan biarkan field ini di-mass-assign langsung dari request tanpa whitelist eksplisit di Form Request.

## 5. Frontend (Blade + Tailwind + Alpine.js)

- Ikuti token warna/tipografi di `01-DESIGN.md` (palet cerah: Petal Cream, Plum Ink, Marigold, Sky Ribbon, Blossom Pink, Meadow Green, Poppy Red) — jangan improvisasi warna baru di luar tabel token, dan jangan pudarkan warna aksen jadi pastel redup (itu justru menghapus alasan palet ini dipilih). Sebaliknya, jangan juga menumpuk banyak warna aksen jenuh berdekatan tanpa jeda Petal Cream/Plum Ink — lihat aturan pemakaian di `01-DESIGN.md` §2.
- Struktur komponen: elemen berulang (label gantung koleksi, badge stempel status, strip ketersediaan tanggal, nav) wajib jadi Blade component (`resources/views/components/`), bukan copy-paste HTML di tiap halaman.
- Komponen strip ketersediaan tanggal & kalender fitting **wajib** menerima data ketersediaan dari backend (hasil query `rentals`/`fittings`), bukan dihitung/di-mock di sisi Alpine — Alpine hanya untuk interaksi (swing animation, buka/tutup, pilih slot), bukan sumber kebenaran data.
- Kalau mengambil referensi pola dari 21st.dev (khususnya komponen date-picker/calendar): **porting manual** markup + Tailwind class ke Blade component, ganti interaktivitas React (`useState`, dsb) dengan Alpine.js (`x-data`, `x-on:click`). Jangan biarkan JSX mentah tertinggal di file Blade.
- Alternatif (kalau nanti proyek dipindah ke Inertia+React supaya komponen 21st.dev bisa dipasang langsung via `npx shadcn add`): itu perubahan arsitektur besar, bukan tempelan — harus didiskusikan ulang sebelum dieksekusi oleh Cursor, jangan diputuskan sepihak oleh AI di tengah jalan.
- Motion terbatas: hanya elemen signature (swing label + buka strip tanggal) yang dianimasikan. Hormati `prefers-reduced-motion`.

## 6. Testing

- Setiap fitur penyewaan wajib ada test kasus: tanggal bentrok untuk item yang sama (harus gagal dengan pesan jelas, bukan lolos lalu error di tengah jalan), dua request bersamaan mencoba memesan tanggal yang sama (harus salah satu gagal, bukan dua-duanya sukses), penyewaan terlambat (status berubah otomatis + denda terhitung benar).
- Setiap fitur fitting wajib ada test kasus: slot penuh (harus gagal dengan pesan jelas), dua request bersamaan merebut slot terakhir (harus salah satu gagal).
- Feature test minimal untuk: login/register, CRUD item/paket (admin only, ditolak untuk member), alur penuh cek-tanggal → fitting → sewa → kembalikan.

## 7. Git & Deployment

- Branch: `main` (stabil), `develop`, `feature/nama-fitur`.
- Tidak ada migration yang di-edit setelah pernah di-push & dijalankan tim lain — buat migration baru untuk perubahan schema lanjutan (termasuk perubahan pada exclusion constraint).
- Sebelum deploy: pastikan backup database terbaru ada, migration sudah dites di staging (termasuk memastikan extension `btree_gist` aktif di server production), ada rencana rollback.

---

### [CURSOR RULE] — versi ringkas untuk `.cursor/rules/weddingkita.mdc`

```
- Selalu gunakan Eloquent/Query Builder dengan parameter binding, tidak pernah raw string concatenation SQL.
- Controller harus thin; logika bisnis wajib di app/Services/.
- Semua listing data wajib pakai pagination, tidak pernah SELECT * tanpa batas.
- Ketersediaan sewa WAJIB divalidasi dua lapis: exclusion constraint (daterange, btree_gist) di database DAN
  pengecekan di RentalService — jangan hanya andalkan salah satu.
- Booking fitting wajib dalam DB::transaction() + lockForUpdate() saat cek kapasitas slot.
- Validasi input selalu lewat Form Request server-side.
- Ikuti token warna & tipografi cerah di 01-DESIGN.md, jangan buat warna baru, jangan dipudarkan jadi pastel.
- Komponen UI berulang wajib jadi Blade component, bukan HTML tercopy-paste.
- Data ketersediaan tanggal & slot fitting datang dari backend, Alpine.js hanya untuk interaksi.
- Jangan matikan CSRF protection.
- Jangan commit .env atau kredensial apapun.
```
