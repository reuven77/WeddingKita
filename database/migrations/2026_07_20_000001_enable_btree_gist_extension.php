<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Aktifkan extension btree_gist di PostgreSQL — diperlukan untuk
     * EXCLUDE USING gist() constraint di tabel rentals.
     * Lihat: 03-RULES.md §1
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist;');
        DB::statement("
            CREATE OR REPLACE FUNCTION timestamptz_to_date_immutable(t timestamptz)
            RETURNS date AS $$
                SELECT (t AT TIME ZONE 'UTC')::date;
            $$ LANGUAGE sql IMMUTABLE;
        ");
    }


    /**
     * Reverse the migrations.
     * Jangan drop extension di production — bisa berdampak ke tabel lain.
     */
    public function down(): void
    {
        // Sengaja tidak di-drop: extension bersifat global per database
        // dan bisa dipakai oleh objek lain.
    }
};
