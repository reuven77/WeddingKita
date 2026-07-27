<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasUuids;

    /**
     * SATU unit fisik gaun/jas/aksesoris.
     * Ketersediaan ditentukan oleh exclusion constraint di rentals,
     * bukan kolom stock.
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'call_code',
        'size_label',
        'cover_image_path',
        'base_price',
        'deposit_amount',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_price'     => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Paket yang mengandung item ini */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_items');
    }

    /** @return HasMany<Rental, $this> */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /** @return HasMany<Fitting, $this> */
    public function fittings(): HasMany
    {
        return $this->hasMany(Fitting::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isAvailable(): bool
    {
        return $this->status === 'aktif';
    }
}
