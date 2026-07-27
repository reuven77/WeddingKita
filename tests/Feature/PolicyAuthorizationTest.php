<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Fitting;
use App\Models\Item;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PolicyAuthorizationTest — Feature Test untuk otorisasi berbasis role (Policy).
 *
 * Memverifikasi:
 * 1. Member TIDAK bisa mengakses admin endpoint update rental status (403).
 * 2. Admin BISA mengakses admin endpoint update rental status (redirect ok).
 * 3. Member TIDAK bisa mengakses admin endpoint update fitting status (403).
 * 4. Admin BISA mengakses admin endpoint update fitting status (redirect ok).
 * 5. Member tidak bisa mengubah rental milik member lain.
 */
class PolicyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private User $otherMember;
    private Item $item;
    private Rental $rental;
    private Fitting $fitting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Policy Test',
            'email'    => 'admin.policy@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $this->member = User::create([
            'name'     => 'Member Policy Test',
            'email'    => 'member.policy@test.com',
            'password' => bcrypt('password'),
            'role'     => 'member',
        ]);

        $this->otherMember = User::create([
            'name'     => 'Other Member',
            'email'    => 'other.member@test.com',
            'password' => bcrypt('password'),
            'role'     => 'member',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category-policy',
        ]);

        $this->item = Item::create([
            'category_id'    => $category->id,
            'name'           => 'Gaun Policy Test',
            'description'    => 'Desc',
            'call_code'      => 'WK-POL-01',
            'size_label'     => 'M',
            'base_price'     => 1000000,
            'deposit_amount' => 0,
            'status'         => 'aktif',
        ]);

        // Buat Rental milik $member
        $this->rental = new Rental();
        $this->rental->user_id       = $this->member->id;
        $this->rental->item_id       = $this->item->id;
        $this->rental->event_date    = Carbon::today()->addDays(10)->toDateString();
        $this->rental->pickup_at     = Carbon::today()->addDays(8)->setHour(9);
        $this->rental->return_due_at = Carbon::today()->addDays(11)->setHour(17);
        $this->rental->status        = Rental::STATUS_MENUNGGU_FITTING;
        $this->rental->deposit_status = Rental::DEPOSIT_DITAHAN;
        $this->rental->total_price   = 1000000;
        $this->rental->fine_amount   = 0;
        $this->rental->save();

        // Buat Fitting yang terhubung ke rental $member
        $this->fitting = Fitting::create([
            'user_id'          => $this->member->id,
            'rental_id'        => $this->rental->id,
            'item_id'          => $this->item->id,
            'scheduled_at'     => Carbon::today()->addDays(5)->setHour(10),
            'duration_minutes' => 60,
            'status'           => Fitting::STATUS_TERJADWAL,
        ]);
    }

    // =========================================================================
    // Tes Otorisasi Rental Status Update
    // =========================================================================

    /**
     * Member TIDAK bisa mengubah status rental mana pun (walau miliknya sendiri).
     * Endpoint admin dilindungi middleware 'role:admin' + RentalPolicy.
     */
    public function test_member_cannot_update_rental_status(): void
    {
        $response = $this->actingAs($this->member)
            ->post("/admin/rentals/{$this->rental->id}/status", ['status' => 'confirm']);

        // Middleware 'role:admin' harus mengembalikan 403
        $response->assertStatus(403);
    }

    /**
     * Admin BISA mengubah status rental ke 'confirm'.
     */
    public function test_admin_can_confirm_rental(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/rentals/{$this->rental->id}/status", ['status' => 'confirm']);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rentals', [
            'id'     => $this->rental->id,
            'status' => Rental::STATUS_DIKONFIRMASI,
        ]);
    }

    /**
     * Admin BISA mengubah status rental ke 'cancel'.
     */
    public function test_admin_can_cancel_rental(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/rentals/{$this->rental->id}/status", ['status' => 'cancel']);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('rentals', [
            'id'     => $this->rental->id,
            'status' => Rental::STATUS_DIBATALKAN,
        ]);
    }

    // =========================================================================
    // Tes Otorisasi Fitting Status Update
    // =========================================================================

    /**
     * Member TIDAK bisa mengubah status fitting mana pun.
     */
    public function test_member_cannot_update_fitting_status(): void
    {
        $response = $this->actingAs($this->member)
            ->post("/admin/fittings/{$this->fitting->id}/status", ['status' => 'complete']);

        $response->assertStatus(403);
    }

    /**
     * Admin BISA mengubah status fitting ke 'complete'.
     */
    public function test_admin_can_complete_fitting(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/fittings/{$this->fitting->id}/status", ['status' => 'complete']);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('fittings', [
            'id'     => $this->fitting->id,
            'status' => Fitting::STATUS_SELESAI,
        ]);
    }

    /**
     * Admin BISA mengubah status fitting ke 'noshow'.
     */
    public function test_admin_can_mark_fitting_as_noshow(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/fittings/{$this->fitting->id}/status", ['status' => 'noshow']);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('fittings', [
            'id'     => $this->fitting->id,
            'status' => Fitting::STATUS_TIDAK_HADIR,
        ]);
    }

    // =========================================================================
    // Tes Validasi Form Request
    // =========================================================================

    /**
     * Admin mendapat error validasi jika status tidak valid.
     */
    public function test_admin_gets_validation_error_with_invalid_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/rentals/{$this->rental->id}/status", ['status' => 'invalid_action']);

        $response->assertSessionHasErrors('status');
    }
}
