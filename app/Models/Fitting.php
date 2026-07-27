<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fitting extends Model
{
    use HasUuids;

    /**
     * Kapasitas: 5 ruang fitting paralel per slot waktu.
     * Validasi atomik ada di FittingService dengan lockForUpdate().
     * Lihat: 03-RULES.md §2.
     */
    protected $fillable = [
        'user_id',
        'rental_id',
        'item_id',
        'package_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at'     => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    // =========================================================================
    // Constants
    // =========================================================================

    const STATUS_TERJADWAL   = 'terjadwal';
    const STATUS_SELESAI     = 'selesai';
    const STATUS_BATAL       = 'batal';
    const STATUS_TIDAK_HADIR = 'tidak_hadir';

    /** Kapasitas fitting per slot (02-PRD.md §11) */
    const SLOT_CAPACITY = 5;

    // =========================================================================
    // Relasi
    // =========================================================================

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Rental, $this> */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** @return BelongsTo<Package, $this> */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
