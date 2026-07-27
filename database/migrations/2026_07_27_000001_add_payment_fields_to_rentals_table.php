<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom pembayaran ke tabel rentals.
     *
     * Status baru yang ditambahkan:
     *   - menunggu_pembayaran   : fitting selesai, menunggu member bayar
     *   - pembayaran_dikonfirmasi: admin sudah konfirmasi, baju bisa diambil
     *
     * Payment status:
     *   - menunggu          : belum ada pembayaran
     *   - menunggu_konfirmasi: bukti sudah diupload, menunggu admin
     *   - lunas             : admin sudah konfirmasi lunas
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Drop CHECK constraint lama pada status (perlu recreate dengan nilai baru)
            // Kolom payment
            $table->string('payment_status')->default('menunggu')->after('deposit_status');
            // enum: menunggu, menunggu_konfirmasi, lunas

            $table->decimal('payment_amount', 12, 2)->default(0)->after('payment_status');
            $table->string('payment_proof_path')->nullable()->after('payment_amount');
            $table->text('payment_notes')->nullable()->after('payment_proof_path');
            $table->timestampTz('payment_confirmed_at')->nullable()->after('payment_notes');
            $table->uuid('payment_confirmed_by')->nullable()->after('payment_confirmed_at');
        });

        // Drop & recreate CHECK constraint status dengan nilai baru
        DB::statement("ALTER TABLE rentals DROP CONSTRAINT check_rental_status");
        DB::statement("
            ALTER TABLE rentals ADD CONSTRAINT check_rental_status
            CHECK (status IN (
                'menunggu_fitting',
                'menunggu_pembayaran',
                'pembayaran_dikonfirmasi',
                'dikonfirmasi',
                'sedang_disewa',
                'dikembalikan',
                'terlambat',
                'dibatalkan'
            ))
        ");

        // CHECK constraint untuk payment_status
        DB::statement("
            ALTER TABLE rentals ADD CONSTRAINT check_payment_status
            CHECK (payment_status IN ('menunggu', 'menunggu_konfirmasi', 'lunas'))
        ");

        // Index payment_status untuk query antrian pembayaran
        Schema::table('rentals', function (Blueprint $table) {
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        // Drop kolom payment
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_amount',
                'payment_proof_path',
                'payment_notes',
                'payment_confirmed_at',
                'payment_confirmed_by',
            ]);
            $table->dropIndex(['payment_status']);
        });

        // Restore CHECK constraint status ke versi lama
        DB::statement("ALTER TABLE rentals DROP CONSTRAINT check_rental_status");
        DB::statement("ALTER TABLE rentals DROP CONSTRAINT IF EXISTS check_payment_status");
        DB::statement("
            ALTER TABLE rentals ADD CONSTRAINT check_rental_status
            CHECK (status IN (
                'menunggu_fitting',
                'dikonfirmasi',
                'sedang_disewa',
                'dikembalikan',
                'terlambat',
                'dibatalkan'
            ))
        ");
    }
};
