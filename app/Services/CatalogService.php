<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * CatalogService — logika bisnis tampilan katalog koleksi.
 *
 * Tanggung jawab:
 * - Filter item/paket berdasarkan tanggal acara (cek ketersediaan)
 * - Filter berdasarkan kategori
 * - Pagination hasil pencarian
 * - Logika full-text search (PostgreSQL tsvector — fase MVP pakai ILIKE,
 *   migrasi ke tsvector di fase lanjut sesuai PRD §8)
 *
 * Semua listing wajib pakai paginate() — tidak pernah get() tanpa batas.
 * Lihat: 03-RULES.md §2.
 */
class CatalogService
{
    public function __construct(
        private readonly RentalService $rentalService
    ) {}

    // =========================================================================
    // Catalog Items
    // =========================================================================

    /**
     * Ambil daftar item dengan filter opsional.
     * Selalu paginated — tidak pernah SELECT * semua baris.
     *
     * @param  array{
     *   category_slug?: string|null,
     *   event_date?: string|null,
     *   search?: string|null,
     *   status?: string,
     *   per_page?: int,
     * }  $filters
     *
     * @return LengthAwarePaginator<Item>
     */
    public function getItems(array $filters = []): LengthAwarePaginator
    {
        $query = Item::query()
            ->select([
                'items.id', 'items.category_id', 'items.name', 'items.call_code',
                'items.size_label', 'items.cover_image_path',
                'items.base_price', 'items.status',
            ])
            ->with(['category:id,name,slug'])
            ->where('items.status', $filters['status'] ?? 'aktif');

        // Filter kategori
        if (! empty($filters['category_slug'])) {
            $query->whereHas('category', function ($q) use ($filters): void {
                $q->where('slug', $filters['category_slug']);
            });
        }

        // Filter tanggal acara — cek ketersediaan
        if (! empty($filters['event_date'])) {
            $query = $this->filterByEventDateAvailability(
                $query,
                $filters['event_date']
            );
        }

        // Search (MVP: ILIKE — upgrade ke tsvector di fase lanjut)
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('items.name', 'ILIKE', "%{$search}%")
                  ->orWhere('items.call_code', 'ILIKE', "%{$search}%")
                  ->orWhere('items.description', 'ILIKE', "%{$search}%");
            });
        }

        return $query
            ->orderBy('items.call_code')
            ->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Ambil daftar paket dengan filter opsional.
     *
     * @param  array{
     *   category_slug?: string|null,
     *   event_date?: string|null,
     *   search?: string|null,
     *   per_page?: int,
     * }  $filters
     *
     * @return LengthAwarePaginator<Package>
     */
    public function getPackages(array $filters = []): LengthAwarePaginator
    {
        $query = Package::query()
            ->select([
                'packages.id', 'packages.category_id', 'packages.name',
                'packages.slug', 'packages.cover_image_path',
                'packages.base_price', 'packages.deposit_amount',
            ])
            ->with(['category:id,name,slug', 'items:id,call_code,name,size_label']);

        // Filter kategori
        if (! empty($filters['category_slug'])) {
            $query->whereHas('category', function ($q) use ($filters): void {
                $q->where('slug', $filters['category_slug']);
            });
        }

        // Filter tanggal acara — cek ketersediaan paket
        if (! empty($filters['event_date'])) {
            $query = $this->filterPackagesByEventDateAvailability(
                $query,
                $filters['event_date']
            );
        }

        // Search
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('packages.name', 'ILIKE', "%{$search}%")
                  ->orWhere('packages.description', 'ILIKE', "%{$search}%");
            });
        }

        return $query
            ->orderBy('packages.name')
            ->paginate($filters['per_page'] ?? 12);
    }

    // =========================================================================
    // Kategori
    // =========================================================================

    /**
     * Ambil semua kategori (untuk navigasi / filter pita warna).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Category>
     */
    public function getAllCategories()
    {
        return Category::query()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Filter query item berdasarkan ketersediaan untuk tanggal acara.
     * Mengecualikan item yang sudah memiliki rental aktif yang
     * tumpang tindih dengan estimasi pickup/return untuk tanggal tersebut.
     *
     * Estimasi pickup = H-2 dari event_date (jam 09:00)
     * Estimasi return = H+1 dari event_date (jam 17:00)
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Item>  $query
     * @param  string  $eventDate  Format: Y-m-d
     * @return \Illuminate\Database\Eloquent\Builder<Item>
     */
    private function filterByEventDateAvailability(
        \Illuminate\Database\Eloquent\Builder $query,
        string $eventDate
    ): \Illuminate\Database\Eloquent\Builder {
        $estimatedPickup    = Carbon::parse($eventDate)->subDays(2)->setHour(9);
        $estimatedReturnDue = Carbon::parse($eventDate)->addDay()->setHour(17);

        // Exclude item yang punya rental aktif yang overlap dengan estimasi tanggal
        $query->whereNotExists(function ($sub) use ($estimatedPickup, $estimatedReturnDue): void {
            $sub->from('rentals')
                ->whereColumn('rentals.item_id', 'items.id')
                ->whereIn('rentals.status', \App\Models\Rental::blockingStatuses())
                ->where('rentals.pickup_at', '<', $estimatedReturnDue)
                ->where('rentals.return_due_at', '>', $estimatedPickup);
        });

        return $query;
    }

    /**
     * Filter query paket berdasarkan ketersediaan item-item di dalamnya untuk tanggal acara.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Package>  $query
     * @param  string  $eventDate  Format: Y-m-d
     * @return \Illuminate\Database\Eloquent\Builder<Package>
     */
    private function filterPackagesByEventDateAvailability(
        \Illuminate\Database\Eloquent\Builder $query,
        string $eventDate
    ): \Illuminate\Database\Eloquent\Builder {
        $estimatedPickup    = Carbon::parse($eventDate)->subDays(2)->setHour(9);
        $estimatedReturnDue = Carbon::parse($eventDate)->addDay()->setHour(17);

        // Exclude paket jika ADA item fisiknya yang sedang ter-booking di tanggal tersebut
        $query->whereDoesntHave('items', function ($itemQuery) use ($estimatedPickup, $estimatedReturnDue): void {
            $itemQuery->whereExists(function ($sub): void {
                $sub->from('rentals')
                    ->whereColumn('rentals.item_id', 'items.id')
                    ->whereIn('rentals.status', \App\Models\Rental::blockingStatuses())
                    ->where('rentals.pickup_at', '<', $estimatedPickup)
                    ->where('rentals.return_due_at', '>', $estimatedReturnDue);
            });
        });

        return $query;
    }
}

