<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Package;
use App\Services\CatalogService;
use App\Services\FittingService;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly RentalService $rentalService,
        private readonly FittingService $fittingService
    ) {}

    /**
     * Tampilan Beranda & Pencarian Katalog
     */
    public function index(Request $request)
    {
        // Validasi input tanggal acara (jika dicari)
        $request->validate([
            'event_date' => 'nullable|date|after_or_equal:today',
            'category' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        $eventDate = $request->input('event_date');
        $categorySlug = $request->input('category');
        $search = $request->input('search');

        // Tarik Kategori (untuk tab pita warna)
        $categories = $this->catalogService->getAllCategories();

        // Tarik Items & Packages sesuai filter
        $items = $this->catalogService->getItems([
            'category_slug' => $categorySlug,
            'event_date' => $eventDate,
            'search' => $search,
            'per_page' => 8,
        ]);

        $packages = $this->catalogService->getPackages([
            'category_slug' => $categorySlug,
            'search' => $search,
            'per_page' => 4,
        ]);

        // Hitung ketersediaan 14 hari ke depan untuk tiap item
        $availabilities = [];
        foreach ($items as $item) {
            $availabilities[$item->id] = $this->rentalService->getAvailabilityForNext14Days($item->id);
        }

        return view('welcome', [
            'categories' => $categories,
            'items' => $items,
            'packages' => $packages,
            'availabilities' => $availabilities,
            'selectedCategory' => $categorySlug,
            'eventDate' => $eventDate,
            'search' => $search,
        ]);
    }

    /**
     * Tampilan Detail Item
     */
    public function showItem(Request $request, string $id)
    {
        $item = Item::with(['category', 'reviews.user'])->findOrFail($id);

        $eventDate = $request->input('event_date');
        $dateParam = $request->input('date'); // Untuk tanggal fitting calendar
        $selectedDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();

        // Get 14 days availability strip
        $availability = $this->rentalService->getAvailabilityForNext14Days($item->id);

        // Get fitting slots status for selected date
        $fittingSlots = $this->fittingService->getSlotCapacityForDay($selectedDate);

        return view('pages.item-detail', [
            'item' => $item,
            'availability' => $availability,
            'fittingSlots' => $fittingSlots,
            'selectedDate' => $selectedDate,
            'eventDate' => $eventDate,
        ]);
    }

    /**
     * Tampilan Detail Paket
     */
    public function showPackage(Request $request, string $id)
    {
        $package = Package::with(['category', 'items', 'services', 'reviews.user'])->findOrFail($id);

        $eventDate = $request->input('event_date');
        $dateParam = $request->input('date'); // Untuk tanggal fitting calendar
        $selectedDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();

        // Get fitting slots status for selected date
        $fittingSlots = $this->fittingService->getSlotCapacityForDay($selectedDate);

        // Paket ketersediaannya tergantung ketersediaan seluruh item di dalamnya
        $packageAvailability = [];
        $today = Carbon::today();
        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $dateStr = $date->toDateString();

            // Paket tersedia jika seluruh item di dalamnya tersedia untuk tanggal ini
            $isAvailable = true;
            foreach ($package->items as $item) {
                // Check if this item is booked for this date
                $itemAvail = $this->rentalService->getAvailabilityForNext14Days($item->id);
                if (!($itemAvail[$dateStr] ?? true)) {
                    $isAvailable = false;
                    break;
                }
            }
            $packageAvailability[$dateStr] = $isAvailable;
        }

        return view('pages.package-detail', [
            'package' => $package,
            'availability' => $packageAvailability,
            'fittingSlots' => $fittingSlots,
            'selectedDate' => $selectedDate,
            'eventDate' => $eventDate,
        ]);
    }
}
