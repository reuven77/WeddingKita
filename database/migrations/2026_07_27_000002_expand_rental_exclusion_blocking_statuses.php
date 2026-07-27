<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perluas exclusion constraint agar slot tanggal terkunci sejak booking
     * (menunggu_fitting), bukan hanya setelah dikonfirmasi.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE rentals DROP CONSTRAINT IF EXISTS no_overlapping_rental');

        DB::statement("
            ALTER TABLE rentals
            ADD CONSTRAINT no_overlapping_rental
            EXCLUDE USING gist (
                item_id WITH =,
                daterange(timestamptz_to_date_immutable(pickup_at), timestamptz_to_date_immutable(return_due_at), '[]') WITH &&
            )
            WHERE (
                status IN (
                    'menunggu_fitting',
                    'menunggu_pembayaran',
                    'pembayaran_dikonfirmasi',
                    'dikonfirmasi',
                    'sedang_disewa',
                    'terlambat'
                )
                AND item_id IS NOT NULL
            )
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE rentals DROP CONSTRAINT IF EXISTS no_overlapping_rental');

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
};
