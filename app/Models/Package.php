<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'cover_image_path',
        'base_price',
        'deposit_amount',
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

    /** Item fisik yang termasuk dalam paket ini */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'package_items');
    }

    /** Jasa yang tersedia untuk paket ini */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'package_services')
                    ->withPivot('is_default');
    }

    /** Jasa yang otomatis termasuk (is_default = true) */
    public function defaultServices(): BelongsToMany
    {
        return $this->services()->wherePivot('is_default', true);
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

    /** User yang memfavoritkan paket ini */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
