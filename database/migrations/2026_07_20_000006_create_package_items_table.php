<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel package_items — relasi many-to-many: item fisik apa saja
     * yang termasuk dalam satu paket.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('package_items', function (Blueprint $table) {
            $table->foreignUuid('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignUuid('item_id')->constrained('items')->cascadeOnDelete();

            $table->primary(['package_id', 'item_id']); // composite PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_items');
    }
};
