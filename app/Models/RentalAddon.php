<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalAddon extends Model
{
    use HasUuids;

    /**
     * price_at_booking disimpan sebagai snapshot harga saat booking —
     * perubahan harga jasa di masa depan tidak mempengaruhi transaksi historis.
     * Tidak ada timestamps (PRD §5 tidak mencantumkannya).
     */
    public $timestamps = false;

    protected $fillable = [
        'rental_id',
        'service_id',
        'price_at_booking',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_at_booking' => 'decimal:2',
        ];
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    /** @return BelongsTo<Rental, $this> */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
