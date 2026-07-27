<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Fitting;
use App\Models\Item;
use App\Models\User;
use App\Services\FittingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FittingServiceTest extends TestCase
{
    use RefreshDatabase;

    private FittingService $fittingService;
    private User $member;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fittingService = new FittingService();
        
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
     * Test slot fitting masih tersedia jika di bawah kapasitas (maksimal 5).
     */
    public function test_slot_is_available_under_capacity(): void
    {
        $scheduledAt = Carbon::today()->addDays(2)->setHour(10)->setMinute(0);

        // Buat 3 fitting di slot yang sama
        for ($i = 0; $i < 3; $i++) {
            Fitting::create([
                'user_id' => $this->member->id,
                'item_id' => $this->item->id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => 60,
                'status' => Fitting::STATUS_TERJADWAL,
            ]);
        }

        // Kapasitas adalah 5. Jumlah terisi 3, maka harusnya masih tersedia.
        $this->assertTrue($this->fittingService->isSlotAvailable($scheduledAt));
    }

    /**
     * Test slot fitting penuh ketika mencapai batas maksimal 5 paralel sesi.
     */
    public function test_slot_is_full_at_max_capacity(): void
    {
        $scheduledAt = Carbon::today()->addDays(2)->setHour(10)->setMinute(0);

        // Isi slot penuh (buat 5 fitting paralel)
        for ($i = 0; $i < 5; $i++) {
            Fitting::create([
                'user_id' => $this->member->id,
                'item_id' => $this->item->id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => 60,
                'status' => Fitting::STATUS_TERJADWAL,
            ]);
        }

        // Sisa kapasitas harusnya 0. Slot penuh.
        $this->assertFalse($this->fittingService->isSlotAvailable($scheduledAt));

        // Mencoba membuat fitting ke-6 di jam tersebut harus melempar RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Slot fitting untuk waktu tersebut sudah penuh.');

        $this->fittingService->createFitting([
            'user_id' => $this->member->id,
            'item_id' => $this->item->id,
            'scheduled_at' => $scheduledAt->toDateTimeString(),
            'duration_minutes' => 60,
        ]);
    }
}
