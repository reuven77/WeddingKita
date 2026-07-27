<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel rentals — transaksi sewa busana pengantin.
     * PENTING:
     * 1. Uang pakai NUMERIC (bukan FLOAT) — 03-RULES.md §1
     * 2. Semua timestamp pakai TIMESTAMPTZ — disimpan UTC
     * 3. Exclusion constraint berbasis daterange + btree_gist untuk
     *    mencegah overlap tanggal sewa per item fisik — 03-RULES.md §1
     *    Ini garis pertahanan terakhir; validasi di RentalService tetap wajib.
     * 4. CHECK (item_id IS NOT NULL OR package_id IS NOT NULL)
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignUuid('package_id')->nullable()->constrained('packages')->nullOnDelete();

            $table->date('event_date');                          // DATE — tanggal acara utama
            $table->timestampTz('pickup_at');                   // TIMESTAMPTZ — saat ambil busana
            $table->timestampTz('return_due_at');               // TIMESTAMPTZ — tenggat kembali
            $table->timestampTz('returned_at')->nullable();     // TIMESTAMPTZ — saat dikembalikan

            $table->string('status')->default('menunggu_fitting');
            // enum: menunggu_fitting, dikonfirmasi, sedang_disewa, dikembalikan, terlambat, dibatalkan

            $table->string('deposit_status')->default('ditahan');
            // enum: ditahan, dikembalikan, dipotong_denda
            // Catatan: per 02-PRD.md §11, tidak ada skema jaminan deposit — deposit_amount = 0
            // Field ini dipertahankan untuk audit trail

            $table->decimal('fine_amount', 12, 2)->default(0);  // NUMERIC — denda (Rp 20.000/hari)
            $table->decimal('total_price', 12, 2);              // NUMERIC — total harga sewa + add-on

            $table->timestamps();
        });

        // CHECK: harus isi salah satu — item_id atau package_id
        DB::statement("ALTER TABLE rentals ADD CONSTRAINT check_rental_target CHECK ((item_id IS NOT NULL) OR (package_id IS NOT NULL))");

        // CHECK constraints untuk enum status
        DB::statement("ALTER TABLE rentals ADD CONSTRAINT check_rental_status CHECK (status IN ('menunggu_fitting', 'dikonfirmasi', 'sedang_disewa', 'dikembalikan', 'terlambat', 'dibatalkan'))");
        DB::statement("ALTER TABLE rentals ADD CONSTRAINT check_rental_deposit_status CHECK (deposit_status IN ('ditahan', 'dikembalikan', 'dipotong_denda'))");


        // Index eksplisit sesuai 02-PRD.md §5 & 03-RULES.md §1
        Schema::table('rentals', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('item_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('event_date');
        });

        // =====================================================================
        // EXCLUSION CONSTRAINT — mencegah overlap tanggal sewa per item fisik
        // Sesuai 03-RULES.md §1 — pengganti "cek stock > 0":
        //   Dua rental untuk item_id yang sama tidak boleh punya rentang
        //   [pickup_at::date, return_due_at::date] yang tumpang tindih,
        //   selama status salah satunya adalah 'dikonfirmasi' atau 'sedang_disewa'.
        //
        // Requires: btree_gist extension (migration 2026_07_20_000001)
        // Error code PostgreSQL: 23P01 (exclusion_violation) — ditangkap di RentalService
        // =====================================================================
        DB::statement("
            ALTER TABLE rentals
            ADD CONSTRAINT no_overlapping_rental
            EXCLUDE USING gist (
                item_id WITH =,
                daterange(timestamptz_to_date_immutable(pickup_at), timestamptz_to_date_immutable(return_due_at), '[]') WITH &&
            )
            WHERE (status IN ('dikonfirmasi', 'sedang_disewa'))
        ");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
