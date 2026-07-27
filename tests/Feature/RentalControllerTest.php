<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Package;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RentalControllerTest — Feature Test untuk endpoint POST /rentals.
 *
 * Memverifikasi:
 * 1. Guest diarahkan ke login.
 * 2. Validasi Form Request menolak payload kosong/tidak valid.
 * 3. Booking berhasil membuat Rental + Fitting baru.
 * 4. Booking ditolak jika item_id DAN package_id sama-sama kosong.
 */
class RentalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $member;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = User::create([
            'name'     => 'Member Test',
            'email'    => 'member@test.com',
            'password' => bcrypt('password'),
            'role'     => 'member',
        ]);

        $category = Category::create([
            'name' => 'Gaun Test',
            'slug' => 'gaun-test-ctrl',
        ]);

        $this->item = Item::create([
            'category_id'    => $category->id,
            'name'           => 'Gaun Uji Controller',
            'description'    => 'Deskripsi gaun uji controller',
            'call_code'      => 'WK-CTRL-01',
            'size_label'     => 'M',
            'base_price'     => 1500000.00,
            'deposit_amount' => 0.00,
            'status'         => 'aktif',
        ]);
    }

    /**
     * Guest tidak terautentikasi diarahkan ke halaman login.
     */
    public function test_guest_cannot_store_rental_and_is_redirected(): void
    {
        $response = $this->post('/rentals', [
            'item_id'        => $this->item->id,
            'event_date'     => Carbon::today()->addDays(10)->toDateString(),
            'scheduled_date' => Carbon::today()->addDays(3)->toDateString(),
            'scheduled_time' => '09:00',
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Form Request menolak payload yang tidak mengandung item_id maupun package_id.
     */
    public function test_store_fails_when_both_item_and_package_are_missing(): void
    {
        $response = $this->actingAs($this->member)->post('/rentals', [
            'event_date'     => Carbon::today()->addDays(10)->toDateString(),
            'scheduled_date' => Carbon::today()->addDays(3)->toDateString(),
            'scheduled_time' => '10:00',
        ]);

        // StoreRentalRequest::withValidator() menambahkan error jika kedua field kosong
        $response->assertSessionHasErrors('item_id');
    }

    /**
     * Form Request menolak tanggal acara di masa lalu.
     */
    public function test_store_fails_when_event_date_is_in_the_past(): void
    {
        $response = $this->actingAs($this->member)->post('/rentals', [
            'item_id'        => $this->item->id,
            'event_date'     => Carbon::yesterday()->toDateString(),
            'scheduled_date' => Carbon::today()->addDays(1)->toDateString(),
            'scheduled_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('event_date');
    }

    /**
     * Form Request menolak format jam yang tidak valid.
     */
    public function test_store_fails_when_scheduled_time_format_is_invalid(): void
    {
        $response = $this->actingAs($this->member)->post('/rentals', [
            'item_id'        => $this->item->id,
            'event_date'     => Carbon::today()->addDays(10)->toDateString(),
            'scheduled_date' => Carbon::today()->addDays(3)->toDateString(),
            'scheduled_time' => 'jam_sembilan', // format tidak valid
        ]);

        $response->assertSessionHasErrors('scheduled_time');
    }

    /**
     * Booking berhasil: membuat Rental + Fitting baru dan redirect ke dashboard.
     */
    public function test_member_can_successfully_book_a_rental(): void
    {
        $eventDate = Carbon::today()->addDays(15)->toDateString();
        $fittingDate = Carbon::today()->addDays(5)->toDateString();

        $response = $this->actingAs($this->member)->post('/rentals', [
            'item_id'        => $this->item->id,
            'event_date'     => $eventDate,
            'scheduled_date' => $fittingDate,
            'scheduled_time' => '10:00',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        // Pastikan Rental & Fitting terbuat di database
        $this->assertDatabaseHas('rentals', [
            'user_id'    => $this->member->id,
            'item_id'    => $this->item->id,
            'event_date' => $eventDate,
            'status'     => Rental::STATUS_MENUNGGU_FITTING,
        ]);

        $this->assertDatabaseHas('fittings', [
            'user_id' => $this->member->id,
            'item_id' => $this->item->id,
            'status'  => 'terjadwal',
        ]);
    }
}
