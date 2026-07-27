<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Package;
use App\Models\Rental;
use App\Models\Service;
use App\Models\RentalAddon;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RentalService — logika bisnis penyewaan busana pengantin.
 *
 * Tanggung jawab:
 * - Cek ketersediaan item/paket untuk rentang tanggal (defense in depth:
 *   query aplikasi + menangkap exclusion constraint database)
 * - Hitung total harga + add-on jasa
 * - Buat transaksi rental dalam DB::transaction()
 * - Hitung & update denda keterlambatan (Rp 20.000/hari)
 * - Update status rental (pickup, pengembalian)
 *
 * TIDAK boleh diakses langsung dari View — selalu lewat Controller.
 * Lihat: 03-RULES.md §3.
 */
class RentalService
{
    /**
     * Denda per hari keterlambatan (dalam Rupiah).
     * Sumber: 02-PRD.md §11
     */
    const FINE_PER_DAY = 20000;

    // =========================================================================
    // Cek Ketersediaan
    // =========================================================================

    /**
     * Cek apakah item fisik tersedia untuk rentang tanggal yang diminta.
     * Ini adalah validasi aplikasi (defense in depth layer 1).
     * Exclusion constraint di database adalah layer 2.
     *
     * @param  string|int  $itemId
     * @param  Carbon      $pickupAt
     * @param  Carbon      $returnDueAt
     * @param  string|null $excludeRentalId  UUID rental yang dikecualikan (untuk edit)
     */
    public function isItemAvailable(
        string $itemId,
        Carbon $pickupAt,
        Carbon $returnDueAt,
        ?string $excludeRentalId = null
    ): bool {
        $query = Rental::query()
            ->where('item_id', $itemId)
            ->whereIn('status', Rental::blockingStatuses())
            // Cek overlap tanggal: ada tumpang tindih jika pickup < return_due_at orang lain
            // DAN return_due > pickup orang lain
            ->where('pickup_at', '<', $returnDueAt)
            ->where('return_due_at', '>', $pickupAt);

        if ($excludeRentalId) {
            $query->where('id', '!=', $excludeRentalId);
        }

        return ! $query->exists();
    }

    /**
     * Ambil tanggal-tanggal yang sudah dipesan untuk item tertentu
     * (untuk ditampilkan di date-availability-strip — 14 hari ke depan).
     *
     * @param  string  $itemId
     * @param  Carbon  $from
     * @param  Carbon  $to
     * @return array<array{pickup_at: string, return_due_at: string}>
     */
    public function getBookedRanges(string $itemId, Carbon $from, Carbon $to): array
    {
        return Rental::query()
            ->select(['pickup_at', 'return_due_at'])
            ->where('item_id', $itemId)
            ->whereIn('status', Rental::blockingStatuses())
            ->where('pickup_at', '<', $to)
            ->where('return_due_at', '>', $from)
            ->get()
            ->toArray();
    }

    /**
     * Ambil status ketersediaan 14 hari ke depan untuk item tertentu.
     * Digunakan oleh komponen date-availability-strip di frontend.
     *
     * @param  string  $itemId
     * @return array<string, bool>  ['2026-10-12' => true (tersedia), '2026-10-13' => false, ...]
     */
    public function getAvailabilityForNext14Days(string $itemId): array
    {
        $from  = Carbon::today();
        $to    = Carbon::today()->addDays(14);

        $bookedRanges = $this->getBookedRanges($itemId, $from, $to);

        $availability = [];
        for ($i = 0; $i < 14; $i++) {
            $date    = $from->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $isBooked = false;
            foreach ($bookedRanges as $range) {
                $pickupDate    = Carbon::parse($range['pickup_at'])->startOfDay();
                $returnDueDate = Carbon::parse($range['return_due_at'])->endOfDay();
                if ($date->between($pickupDate, $returnDueDate)) {
                    $isBooked = true;
                    break;
                }
            }

            $availability[$dateStr] = ! $isBooked;
        }

        return $availability;
    }

    /**
     * Cek ketersediaan semua item fisik dalam paket untuk rentang tanggal.
     */
    public function isPackageAvailable(
        string $packageId,
        Carbon $pickupAt,
        Carbon $returnDueAt,
        ?string $excludeRentalId = null
    ): bool {
        $package = Package::with('items:id')->findOrFail($packageId);

        if ($package->items->isEmpty()) {
            return false;
        }

        foreach ($package->items as $item) {
            if (! $this->isItemAvailable($item->id, $pickupAt, $returnDueAt, $excludeRentalId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ketersediaan paket 14 hari ke depan (semua item harus tersedia).
     *
     * @return array<string, bool>
     */
    public function getPackageAvailabilityForNext14Days(string $packageId): array
    {
        $package = Package::with('items:id')->findOrFail($packageId);
        $from    = Carbon::today();
        $availability = [];

        for ($i = 0; $i < 14; $i++) {
            $date    = $from->copy()->addDays($i);
            $dateStr = $date->toDateString();
            $isAvailable = true;

            foreach ($package->items as $item) {
                $itemAvail = $this->getAvailabilityForNext14Days($item->id);
                if (! ($itemAvail[$dateStr] ?? true)) {
                    $isAvailable = false;
                    break;
                }
            }

            $availability[$dateStr] = $isAvailable;
        }

        return $availability;
    }

    /**
     * Apakah item tersedia untuk satu tanggal acara (event_date)?
     */
    public function isItemAvailableForEventDate(string $itemId, string $eventDate): bool
    {
        $pickupAt    = Carbon::parse($eventDate)->subDays(2)->setHour(9)->setMinute(0);
        $returnDueAt = Carbon::parse($eventDate)->addDay()->setHour(17)->setMinute(0);

        return $this->isItemAvailable($itemId, $pickupAt, $returnDueAt);
    }

    /**
     * Apakah paket tersedia untuk satu tanggal acara?
     */
    public function isPackageAvailableForEventDate(string $packageId, string $eventDate): bool
    {
        $pickupAt    = Carbon::parse($eventDate)->subDays(2)->setHour(9)->setMinute(0);
        $returnDueAt = Carbon::parse($eventDate)->addDay()->setHour(17)->setMinute(0);

        return $this->isPackageAvailable($packageId, $pickupAt, $returnDueAt);
    }

    // =========================================================================
    // Buat Rental
    // =========================================================================

    /**
     * Buat transaksi rental baru.
     *
     * Prosedur:
     * 1. Validasi ketersediaan tanggal (aplikasi)
     * 2. Hitung total harga (item/paket + add-on jasa)
     * 3. Bungkus dalam DB::transaction()
     * 4. Tangkap exclusion constraint violation (PostgreSQL error 23P01)
     *
     * @param  array{
     *   user_id: string,
     *   item_id?: string|null,
     *   package_id?: string|null,
     *   event_date: string,
     *   pickup_at: string,
     *   return_due_at: string,
     *   addon_service_ids?: array<string>,
     * }  $data
     *
     * @throws \RuntimeException  Jika tanggal sudah dipesan atau item tidak tersedia.
     */
    public function createRental(array $data): Rental
    {
        $pickupAt    = Carbon::parse($data['pickup_at']);
        $returnDueAt = Carbon::parse($data['return_due_at']);

        // Layer 1: cek ketersediaan di aplikasi
        if (! empty($data['item_id'])) {
            if (! $this->isItemAvailable($data['item_id'], $pickupAt, $returnDueAt)) {
                throw new \RuntimeException(
                    'Tanggal yang dipilih sudah dipesan untuk item ini. Silakan pilih tanggal lain.'
                );
            }
        } elseif (! empty($data['package_id'])) {
            if (! $this->isPackageAvailable($data['package_id'], $pickupAt, $returnDueAt)) {
                throw new \RuntimeException(
                    'Tanggal yang dipilih sudah dipesan untuk salah satu item dalam paket ini. Silakan pilih tanggal lain.'
                );
            }
        }

        try {
            return DB::transaction(function () use ($data, $pickupAt, $returnDueAt): Rental {
                // Hitung total harga
                $totalPrice = $this->calculateTotalPrice(
                    $data['item_id'] ?? null,
                    $data['package_id'] ?? null,
                    $data['addon_service_ids'] ?? []
                );

                // Buat record rental
                $rental = new Rental();
                $rental->user_id        = $data['user_id'];
                $rental->item_id        = $data['item_id'] ?? null;
                $rental->package_id     = $data['package_id'] ?? null;
                $rental->event_date     = $data['event_date'];
                $rental->pickup_at      = $pickupAt;
                $rental->return_due_at  = $returnDueAt;
                $rental->status         = Rental::STATUS_MENUNGGU_FITTING;
                $rental->deposit_status = Rental::DEPOSIT_DITAHAN;
                $rental->fine_amount    = 0;
                $rental->total_price    = $totalPrice;
                $rental->save();

                // Paket: kunci slot tiap item fisik dengan rental shadow per item
                if (! empty($data['package_id'])) {
                    $this->createPackageItemHolds($rental, $data['package_id'], $pickupAt, $returnDueAt);
                }

                // Buat add-on records
                foreach ($data['addon_service_ids'] ?? [] as $serviceId) {
                    $service = Service::findOrFail($serviceId);

                    RentalAddon::create([
                        'rental_id'        => $rental->id,
                        'service_id'       => $serviceId,
                        'price_at_booking' => $service->price, // snapshot harga saat booking
                    ]);
                }

                Log::info('Rental created', [
                    'rental_id'  => $rental->id,
                    'user_id'    => $rental->user_id,
                    'item_id'    => $rental->item_id,
                    'package_id' => $rental->package_id,
                    'event_date' => $rental->event_date,
                    'total'      => $rental->total_price,
                ]);

                return $rental;
            });
        } catch (QueryException $e) {
            // Layer 2: tangkap exclusion constraint violation dari PostgreSQL
            // Error code 23P01 = exclusion_violation
            if ($e->getCode() === '23P01') {
                Log::warning('Exclusion constraint violation on rental creation', [
                    'item_id'       => $data['item_id'] ?? null,
                    'pickup_at'     => $data['pickup_at'],
                    'return_due_at' => $data['return_due_at'],
                ]);

                throw new \RuntimeException(
                    'Tanggal ini sudah dipesan untuk item tersebut. Silakan pilih tanggal lain.'
                );
            }

            // Error lain — lempar kembali
            throw $e;
        }
    }

    // =========================================================================
    // Update Status
    // =========================================================================

    /**
     * Konfirmasi rental (admin: setelah fitting selesai atau langsung).
     */
    public function confirmRental(Rental $rental): void
    {
        $rental->status = Rental::STATUS_DIKONFIRMASI;
        $rental->save();

        Log::info('Rental confirmed', ['rental_id' => $rental->id]);
    }

    /**
     * Tandai rental sebagai "sedang disewa" (saat pickup).
     */
    public function markAsPickedUp(Rental $rental): void
    {
        $rental->status = Rental::STATUS_SEDANG_DISEWA;
        $rental->save();

        Log::info('Rental picked up', ['rental_id' => $rental->id]);
    }

    /**
     * Proses pengembalian busana.
     * Hitung denda jika terlambat (Rp 20.000/hari).
     */
    public function processReturn(Rental $rental, Carbon $returnedAt): void
    {
        DB::transaction(function () use ($rental, $returnedAt): void {
            $rental->returned_at = $returnedAt;

            // Hitung denda
            $daysLate = (int) $rental->return_due_at->diffInDays($returnedAt, false);
            if ($daysLate > 0) {
                $rental->fine_amount = $daysLate * self::FINE_PER_DAY;
                $rental->status      = Rental::STATUS_TERLAMBAT;
            } else {
                $rental->fine_amount = 0;
                $rental->status      = Rental::STATUS_DIKEMBALIKAN;
            }

            $rental->save();

            Log::info('Rental returned', [
                'rental_id'   => $rental->id,
                'returned_at' => $returnedAt,
                'days_late'   => max(0, $daysLate),
                'fine_amount' => $rental->fine_amount,
            ]);
        });
    }

    /**
     * Batalkan rental.
     */
    public function cancelRental(Rental $rental): void
    {
        $rental->status = Rental::STATUS_DIBATALKAN;
        $rental->save();

        Log::info('Rental cancelled', ['rental_id' => $rental->id]);
    }

    // =========================================================================
    // Kalkulasi Harga
    // =========================================================================

    /**
     * Hitung total harga sewa: item/paket + add-on jasa.
     *
     * @param  string|null   $itemId
     * @param  string|null   $packageId
     * @param  array<string> $addonServiceIds
     */
    private function calculateTotalPrice(
        ?string $itemId,
        ?string $packageId,
        array $addonServiceIds
    ): float {
        $basePrice = 0;

        if ($itemId) {
            $item      = Item::findOrFail($itemId);
            $basePrice = (float) $item->base_price;
        } elseif ($packageId) {
            $package   = Package::findOrFail($packageId);
            $basePrice = (float) $package->base_price;
        }

        // Jumlahkan harga add-on jasa
        $addonTotal = 0;
        if (! empty($addonServiceIds)) {
            $addonTotal = (float) Service::whereIn('id', $addonServiceIds)
                ->sum('price');
        }

        return $basePrice + $addonTotal;
    }

    /**
     * Buat hold record (shadow rental) per item fisik untuk penyewaan paket.
     * Record ini mengunci slot ketersediaan item fisik di PostgreSQL exclusion constraint.
     */
    private function createPackageItemHolds(
        Rental $parentRental,
        string $packageId,
        Carbon $pickupAt,
        Carbon $returnDueAt
    ): void {
        $package = Package::with('items:id')->findOrFail($packageId);

        foreach ($package->items as $item) {
            $childHold = new Rental();
            $childHold->user_id          = $parentRental->user_id;
            $childHold->item_id          = $item->id;
            $childHold->package_id       = null;
            $childHold->parent_rental_id = $parentRental->id;
            $childHold->event_date       = $parentRental->event_date;
            $childHold->pickup_at        = $pickupAt;
            $childHold->return_due_at    = $returnDueAt;
            $childHold->status           = $parentRental->status;
            $childHold->deposit_status   = Rental::DEPOSIT_DITAHAN;
            $childHold->fine_amount      = 0;
            $childHold->total_price      = 0;
            $childHold->save();
        }
    }
}

