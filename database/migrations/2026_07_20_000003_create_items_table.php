<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel items — satu unit fisik gaun/jas/aksesoris.
     * PENTING: satu item fisik = satu baris. Ketersediaan ditentukan
     * oleh exclusion constraint di tabel rentals, bukan kolom stock.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 300);
            $table->text('description')->nullable();
            $table->string('call_code', 20);           // mis. "WK-0142"
            $table->string('size_label', 100)->nullable(); // mis. "M / Lingkar dada 88cm"
            $table->string('cover_image_path')->nullable();
            $table->decimal('base_price', 12, 2);      // NUMERIC, bukan FLOAT — lihat 03-RULES.md §1
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->string('status')->default('aktif'); // enum: aktif, maintenance, nonaktif

            $table->timestamps();
        });

        // CHECK constraint status enum
        DB::statement("ALTER TABLE items ADD CONSTRAINT check_item_status CHECK (status IN ('aktif', 'maintenance', 'nonaktif'))");


        // Index eksplisit sesuai 02-PRD.md §5 & 03-RULES.md §1
        Schema::table('items', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('status');
            $table->index('call_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
