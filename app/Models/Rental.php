<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasUuids;

    /**
     * Data finansial (fine_amount, total_price, payment_amount) TIDAK ada di fillable.
     * Harus diset lewat RentalService / PaymentService — lihat 03-RULES.md §4.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'package_id',
        'parent_rental_id',
        'event_date',
        'pickup_at',
        'return_due_at',
        'returned_at',
        'status',
        'deposit_status',
        // fine_amount, total_price, payment_* dikecualikan — hanya lewat Service layer
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date'           => 'date',
            'pickup_at'            => 'datetime',
            'return_due_at'        => 'datetime',
            'returned_at'          => 'datetime',
            'payment_confirmed_at' => 'datetime',
            'fine_amount'          => 'decimal:2',
            'total_price'          => 'decimal:2',
            'payment_amount'       => 'decimal:2',
        ];
    }

    // =========================================================================
    // Status constants — alur: menunggu_fitting → menunggu_pembayaran
    //                          → pembayaran_dikonfirmasi → sedang_disewa → dikembalikan
    // =========================================================================

    const STATUS_MENUNGGU_FITTING         = 'menunggu_fitting';
    const STATUS_MENUNGGU_PEMBAYARAN      = 'menunggu_pembayaran';
    const STATUS_PEMBAYARAN_DIKONFIRMASI  = 'pembayaran_dikonfirmasi';
    const STATUS_DIKONFIRMASI             = 'dikonfirmasi';  // legacy compat
    const STATUS_SEDANG_DISEWA            = 'sedang_disewa';
    const STATUS_DIKEMBALIKAN             = 'dikembalikan';
    const STATUS_TERLAMBAT                = 'terlambat';
    const STATUS_DIBATALKAN               = 'dibatalkan';

    const DEPOSIT_DITAHAN                 = 'ditahan';
    const DEPOSIT_DIKEMBALIKAN            = 'dikembalikan';
    const DEPOSIT_DIPOTONG_DENDA          = 'dipotong_denda';

    // =========================================================================
    // Payment status constants
    // =========================================================================

    const PAYMENT_MENUNGGU                = 'menunggu';
    const PAYMENT_MENUNGGU_KONFIRMASI     = 'menunggu_konfirmasi';
    const PAYMENT_LUNAS                   = 'lunas';

    /**
     * Status rental yang menahan slot ketersediaan item fisik.
     *
     * @return list<string>
     */
    public static function blockingStatuses(): array
    {
        return [
            self::STATUS_MENUNGGU_FITTING,
            self::STATUS_MENUNGGU_PEMBAYARAN,
            self::STATUS_PEMBAYARAN_DIKONFIRMASI,
            self::STATUS_DIKONFIRMASI,
            self::STATUS_SEDANG_DISEWA,
            self::STATUS_TERLAMBAT,
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

    /** @return HasMany<RentalAddon, $this> */
    public function addons(): HasMany
    {
        return $this->hasMany(RentalAddon::class);
    }

    /** @return HasOne<Fitting, $this> */
    public function fitting(): HasOne
    {
        return $this->hasOne(Fitting::class);
    }

    /** @return BelongsTo<User, $this> */
    public function paymentConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_TERLAMBAT
            || ($this->status === self::STATUS_SEDANG_DISEWA
                && $this->return_due_at->isPast());
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_PEMBAYARAN;
    }

    public function isPaymentConfirmed(): bool
    {
        return $this->status === self::STATUS_PEMBAYARAN_DIKONFIRMASI
            || $this->payment_status === self::PAYMENT_LUNAS;
    }

    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof_path);
    }

    /** Nama item atau paket yang disewa */
    public function rentalSubjectName(): string
    {
        return $this->item?->name ?? $this->package?->name ?? '—';
    }

    /** Nomor invoice human-readable */
    public function invoiceNumber(): string
    {
        return 'WK-INV-' . strtoupper(substr($this->id, 0, 8));
    }

    /**
     * Hitung denda keterlambatan.
     * Denda: Rp 20.000 / hari (02-PRD.md §11)
     */
    public function calculateFine(): int
    {
        if (! $this->returned_at || ! $this->return_due_at) {
            return 0;
        }

        $daysLate = (int) $this->return_due_at->diffInDays($this->returned_at, false);

        return max(0, $daysLate) * 20000;
    }
}
