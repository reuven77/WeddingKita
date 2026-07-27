<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Buat Member
        $this->member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);
    }

    /**
     * Tamu tak terautentikasi (guest) diarahkan ke login saat mengakses dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Member terautentikasi diarahkan ke dashboard area mereka (200 OK)
     */
    public function test_member_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->member)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Area Calon Pengantin');
        $response->assertDontSee('Dashboard Butik');
    }

    /**
     * Admin terautentikasi dapat mengakses dashboard butik (200 OK)
     */
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Butik');
        $response->assertDontSee('Area Calon Pengantin');
    }

    /**
     * Member dilarang keras mengakses endpoint kelola status admin (403 Forbidden).
     */
    public function test_member_cannot_access_admin_action_endpoints(): void
    {
        // Coba kirim POST update status rental ke endpoint admin
        $response = $this->actingAs($this->member)->post('/admin/rentals/any-id/status', [
            'status' => 'pickup',
        ]);

        // Harus mengembalikan 403 Forbidden
        $response->assertStatus(403);
    }
}
