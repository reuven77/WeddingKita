<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel reviews — ulasan member setelah status rental 'dikembalikan'.
     * Tabel favorites — simpan paket favorit member.
     * Sesuai 02-PRD.md §5.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('package_id')->nullable()->constrained('packages')->cascadeOnDelete();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->smallInteger('rating');            // CHECK 1-5
            $table->text('comment');
            $table->timestampTz('created_at');         // TIMESTAMPTZ
        });

        // CHECK rating harus 1-5
        DB::statement("ALTER TABLE reviews ADD CONSTRAINT check_review_rating CHECK (rating >= 1 AND rating <= 5)");
        // CHECK harus ada target review (item atau paket)
        DB::statement("ALTER TABLE reviews ADD CONSTRAINT check_review_target CHECK ((package_id IS NOT NULL) OR (item_id IS NOT NULL))");


        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('package_id');
            $table->index('item_id');
        });

        // Tabel favorites: bookmark paket oleh member
        Schema::create('favorites', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('package_id')->constrained('packages')->cascadeOnDelete();

            $table->primary(['user_id', 'package_id']); // composite PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('reviews');
    }
};
