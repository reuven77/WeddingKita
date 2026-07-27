<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel package_services — jasa opsional yang bisa ditambahkan ke paket.
     * is_default: true = termasuk otomatis, false = opsional tambah biaya.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('package_services', function (Blueprint $table) {
            $table->foreignUuid('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->boolean('is_default')->default(false);

            $table->primary(['package_id', 'service_id']); // composite PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_services');
    }
};
