<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel fittings — jadwal coba baju di butik.
     * Kapasitas: 5 ruang fitting paralel per slot waktu (02-PRD.md §11).
     * Validasi kapasitas wajib dalam DB::transaction() + lockForUpdate()
     * di FittingService — 03-RULES.md §2.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('fittings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('rental_id')->nullable()->constrained('rentals')->nullOnDelete();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignUuid('package_id')->nullable()->constrained('packages')->nullOnDelete();

            $table->timestampTz('scheduled_at');              // TIMESTAMPTZ — waktu fitting
            $table->integer('duration_minutes')->default(60); // default 60 menit
            $table->string('status')->default('terjadwal');
            // enum: terjadwal, selesai, batal, tidak_hadir

            $table->text('notes')->nullable(); // catatan ukuran/permintaan khusus

            $table->timestamps();
        });

        // CHECK constraint status enum
        DB::statement("ALTER TABLE fittings ADD CONSTRAINT check_fitting_status CHECK (status IN ('terjadwal', 'selesai', 'batal', 'tidak_hadir'))");


        // Index eksplisit sesuai 02-PRD.md §5 & 03-RULES.md §1
        Schema::table('fittings', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('rental_id');
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fittings');
    }
};
