<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hold record per item fisik dalam sewa paket (parent_rental_id → transaksi utama).
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->foreignUuid('parent_rental_id')
                ->nullable()
                ->after('package_id')
                ->constrained('rentals')
                ->cascadeOnDelete();

            $table->index('parent_rental_id');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['parent_rental_id']);
            $table->dropColumn('parent_rental_id');
        });
    }
};
