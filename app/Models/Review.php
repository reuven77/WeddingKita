<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasUuids;

    /**
     * Review hanya bisa dibuat setelah status rental 'dikembalikan'.
     * Validasi ada di Form Request.
     * Tidak ada updated_at (PRD §5 hanya menyebutkan created_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'package_id',
        'item_id',
        'rating',
        'comment',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating'     => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Package, $this> */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
