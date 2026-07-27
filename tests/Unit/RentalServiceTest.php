<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\Rental;
use App\Models\User;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalServiceTest extends TestCase
{
    use RefreshDatabase;

    private RentalService $rentalService;
    private User $member;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->rentalService = new RentalService();
        
        // Buat data dummy awal
        $this->member = User::create([
            'name' => 'Member Test',
            'email' => 'member.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $category = Category::create([
            'name' => 'Gaun Test',
            'slug' => 'gaun-test',
        ]);

        $this->item = Item::create([
            'category_id' => $category->id,
            'name' => 'Gaun Uji Coba',
            'description' => 'Deskripsi gaun uji coba',
            'call_code' => 'WK-TEST-01',
            'size_label' => 'M',
            'base_price' => 1000000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);
    }

    /**
     * Test ketersediaan tanggal sewa.
     */
    public function test_item_is_available_when_no_overlapping_rentals(): void
    {
        $pickupAt = Carbon::today()->addDays(2)->setHour(9);
        $returnDueAt = Carbon::today()->addDays(5)->setHour(17);

        $isAvailable = $this->rentalService->isItemAvailable($this->item->id, $pickupAt, $returnDueAt);
        
        $this->assertTrue($isAvailable);
    }

    /**
     * Test deteksi tanggal sewa tumpang tindih (Overlap).
     */
    public function test_item_is_not_available_when_overlapping_rental_exists(): void
    {
        // 1. Buat rental yang dikonfirmasi terlebih dahulu
        $rentalPickup = Carbon::today()->addDays(2)->setHour(9);
        $rentalDue = Carbon::today()->addDays(5)->setHour(17);

        $rental = new Rental();
        $rental->user_id = $this->member->id;
        $rental->item_id = $this->item->id;
        $rental->event_date = Carbon::today()->addDays(4)->toDateString();
        $rental->pickup_at = $rentalPickup;
        $rental->return_due_at = $rentalDue;
        $rental->status = Rental::STATUS_DIKONFIRMASI;
        $rental->deposit_status = Rental::DEPOSIT_DITAHAN;
        $rental->total_price = 1000000.00;
        $rental->save();


        // 2. Cek ketersediaan untuk rentang tanggal yang tumpang tindih
        $checkPickup = Carbon::today()->addDays(3)->setHour(9); // Overlap!
        $checkDue = Carbon::today()->addDays(6)->setHour(17);

        $isAvailable = $this->rentalService->isItemAvailable($this->item->id, $checkPickup, $checkDue);
        
        $this->assertFalse($isAvailable);
    }

    /**
     * Test denda keterlambatan.
     * Denda: Rp 20.000 / hari (02-PRD.md §11)
     */
    public function test_calculate_correct_overdue_fines(): void
    {
        $eventDate = Carbon::today()->subDays(5);
        $pickupAt = $eventDate->copy()->subDays(2)->setHour(9);
        $returnDueAt = $eventDate->copy()->addDay()->setHour(17); // H+1
        $returnedAt = $returnDueAt->copy()->addDays(3); // Terlambat 3 hari

        $rental = new Rental();
        $rental->user_id = $this->member->id;
        $rental->item_id = $this->item->id;
        $rental->event_date = $eventDate->toDateString();
        $rental->pickup_at = $pickupAt;
        $rental->return_due_at = $returnDueAt;
        $rental->status = Rental::STATUS_SEDANG_DISEWA;
        $rental->deposit_status = Rental::DEPOSIT_DITAHAN;
        $rental->total_price = 1000000.00;
        $rental->save();


        // Jalankan proses pengembalian
        $this->rentalService->processReturn($rental, $returnedAt);

        // Nilai denda diharapkan: 3 hari * 20.000 = 60.000
        $this->assertEquals(60000.00, $rental->fresh()->fine_amount);
        $this->assertEquals(Rental::STATUS_TERLAMBAT, $rental->fresh()->status);
    }
}
