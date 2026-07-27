<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCatalogCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Category $category;
    private Category $pkgCategory;
    private Item $item;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Storage fake for image upload tests
        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Admin Owner',
            'email' => 'admin.owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->member = User::create([
            'name' => 'Member Customer',
            'email' => 'member.customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $this->category = Category::create([
            'name' => 'Gaun Modern',
            'slug' => 'gaun-modern',
        ]);

        $this->pkgCategory = Category::create([
            'name' => 'Paket Lengkap',
            'slug' => 'paket-lengkap',
        ]);

        $this->item = Item::create([
            'category_id' => $this->category->id,
            'name' => 'Gaun Satin Velvet Test',
            'call_code' => 'WK-TEST-001',
            'size_label' => 'L',
            'base_price' => 1800000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        $this->service = Service::create([
            'name' => 'Jasa MUA Pro',
            'description' => 'MUA Pro',
            'price' => 1500000.00,
            'is_active' => true,
        ]);
    }

    // =========================================================================
    // ITEM CRUD TESTS
    // =========================================================================

    public function test_member_cannot_access_item_crud_endpoints(): void
    {
        $this->actingAs($this->member);

        $this->get(route('admin.items.index'))->assertStatus(403);
        $this->get(route('admin.items.create'))->assertStatus(403);
        $this->post(route('admin.items.store'), [])->assertStatus(403);
        $this->get(route('admin.items.edit', $this->item->id))->assertStatus(403);
        $this->put(route('admin.items.update', $this->item->id), [])->assertStatus(403);
        $this->delete(route('admin.items.destroy', $this->item->id))->assertStatus(403);
    }

    public function test_admin_can_view_items_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.items.index'));
        $response->assertStatus(200);
        $response->assertSee('WK-TEST-001');
    }

    public function test_admin_can_create_item_with_image(): void
    {
        $file = UploadedFile::fake()->image('dress.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.items.store'), [
            'category_id' => $this->category->id,
            'name' => 'Gaun Brokat Putih',
            'call_code' => 'WK-NEW-99',
            'size_label' => 'M',
            'base_price' => 2000000,
            'deposit_amount' => 0,
            'status' => 'aktif',
            'cover_image' => $file,
        ]);

        $response->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseHas('items', ['call_code' => 'WK-NEW-99']);

        // Check if image is stored in 'public/items' disk
        $item = Item::where('call_code', 'WK-NEW-99')->first();
        $this->assertNotNull($item->cover_image_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $item->cover_image_path));
    }

    public function test_admin_can_update_item(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.items.update', $this->item->id), [
            'category_id' => $this->category->id,
            'name' => 'Gaun Satin Velvet Terupdate',
            'call_code' => 'WK-TEST-001',
            'size_label' => 'L-XL',
            'base_price' => 1900000,
            'deposit_amount' => 0,
            'status' => 'maintenance',
        ]);

        $response->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'name' => 'Gaun Satin Velvet Terupdate',
            'size_label' => 'L-XL',
            'status' => 'maintenance',
        ]);
    }

    public function test_admin_can_delete_item(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.items.destroy', $this->item->id));
        $response->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseMissing('items', ['id' => $this->item->id]);
    }

    // =========================================================================
    // PACKAGE CRUD TESTS
    // =========================================================================

    public function test_member_cannot_access_package_crud_endpoints(): void
    {
        $package = Package::create([
            'name' => 'Paket A',
            'slug' => 'paket-a',
            'category_id' => $this->pkgCategory->id,
            'base_price' => 3000000,
            'deposit_amount' => 0,
        ]);

        $this->actingAs($this->member);

        $this->get(route('admin.packages.index'))->assertStatus(403);
        $this->get(route('admin.packages.create'))->assertStatus(403);
        $this->post(route('admin.packages.store'), [])->assertStatus(403);
        $this->get(route('admin.packages.edit', $package->id))->assertStatus(403);
        $this->put(route('admin.packages.update', $package->id), [])->assertStatus(403);
        $this->delete(route('admin.packages.destroy', $package->id))->assertStatus(403);
    }

    public function test_admin_can_create_package_with_items_and_services(): void
    {
        $file = UploadedFile::fake()->image('package.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.packages.store'), [
            'name' => 'Paket Royal Classic',
            'description' => 'Paket Royal Classic Premium',
            'category_id' => $this->pkgCategory->id,
            'base_price' => 5000000,
            'deposit_amount' => 0,
            'items' => [$this->item->id],
            'services' => [$this->service->id],
            'default_services' => [$this->service->id],
            'cover_image' => $file,
        ]);

        $response->assertRedirect(route('admin.packages.index'));
        
        $package = Package::where('name', 'Paket Royal Classic')->first();
        $this->assertNotNull($package);

        // Verify relationships
        $this->assertTrue($package->items->contains($this->item->id));
        $this->assertTrue($package->services->contains($this->service->id));
        $this->assertEquals(1, $package->services()->where('service_id', $this->service->id)->first()->pivot->is_default);
    }
}
