<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuids;

    /**
     * Tabel services tidak punya timestamps (PRD §5 tidak mencantumkannya).
     */
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // =========================================================================
    // Relasi
    // =========================================================================

    /** Paket yang menawarkan jasa ini */
    public function packages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_services')
                    ->withPivot('is_default');
    }

    /** Rental add-on records yang memakai jasa ini */
    public function rentalAddons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RentalAddon::class);
    }
}
