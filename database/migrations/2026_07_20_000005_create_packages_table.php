<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel packages — paket lengkap (gabungan item + opsional jasa).
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->string('slug', 320)->unique();
            $table->text('description')->nullable();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('cover_image_path')->nullable();
            $table->decimal('base_price', 12, 2);      // NUMERIC
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        // Index FK
        Schema::table('packages', function (Blueprint $table) {
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
