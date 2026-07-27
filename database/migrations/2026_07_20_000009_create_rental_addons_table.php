<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel rental_addons — jasa tambahan yang dipilih untuk 1 transaksi sewa.
     * price_at_booking disimpan agar perubahan harga jasa di masa depan
     * tidak mempengaruhi transaksi historis.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('rental_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rental_id')->constrained('rentals')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->restrictOnDelete();
            $table->decimal('price_at_booking', 12, 2); // NUMERIC — snapshot harga saat booking
        });

        Schema::table('rental_addons', function (Blueprint $table) {
            $table->index('rental_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_addons');
    }
};
