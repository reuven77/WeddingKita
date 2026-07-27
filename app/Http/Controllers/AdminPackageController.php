<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminPackageController extends Controller
{
    /**
     * List all packages.
     */
    public function index()
    {
        $this->authorize('create', Package::class);

        $packages = Package::with(['category', 'items'])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show form to create a package.
     */
    public function create()
    {
        $this->authorize('create', Package::class);

        $categories = Category::all();
        $items = Item::where('status', 'aktif')->orderBy('call_code')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.packages.create', compact('categories', 'items', 'services'));
    }

    /**
     * Store package in DB.
     */
    public function store(StorePackageRequest $request)
    {
        $this->authorize('create', Package::class);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('packages', 'public');
            $data['cover_image_path'] = 'storage/' . $path;
        }

        DB::transaction(function () use ($request, $data) {
            $package = Package::create($data);

            // Attach items
            $package->items()->sync($request->input('items', []));

            // Attach services with default value
            $servicesData = [];
            $services = $request->input('services', []);
            $defaultServices = $request->input('default_services', []);

            foreach ($services as $serviceId) {
                $servicesData[$serviceId] = [
                    'is_default' => in_array($serviceId, $defaultServices)
                ];
            }
            $package->services()->sync($servicesData);
        });

        return redirect()->route('admin.packages.index')->with('success', 'Paket sewa berhasil dibuat.');
    }

    /**
     * Show form to edit package.
     */
    public function edit(Package $package)
    {
        $this->authorize('update', $package);

        $categories = Category::all();
        $items = Item::where('status', 'aktif')->orderBy('call_code')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        // Get currently attached items and services
        $attachedItems = $package->items->pluck('id')->toArray();
        $attachedServices = $package->services->pluck('id')->toArray();
        $defaultServices = $package->services->filter(function($service) {
            return $service->pivot->is_default;
        })->pluck('id')->toArray();

        return view('admin.packages.edit', compact(
            'package', 'categories', 'items', 'services', 
            'attachedItems', 'attachedServices', 'defaultServices'
        ));
    }

    /**
     * Update package.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $this->authorize('update', $package);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('cover_image')) {
            if ($package->cover_image_path) {
                $oldPath = str_replace('storage/', '', $package->cover_image_path);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('cover_image')->store('packages', 'public');
            $data['cover_image_path'] = 'storage/' . $path;
        }

        DB::transaction(function () use ($request, $package, $data) {
            $package->update($data);

            // Sync items
            $package->items()->sync($request->input('items', []));

            // Sync services
            $servicesData = [];
            $services = $request->input('services', []);
            $defaultServices = $request->input('default_services', []);

            foreach ($services as $serviceId) {
                $servicesData[$serviceId] = [
                    'is_default' => in_array($serviceId, $defaultServices)
                ];
            }
            $package->services()->sync($servicesData);
        });

        return redirect()->route('admin.packages.index')->with('success', 'Paket sewa berhasil diperbarui.');
    }

    /**
     * Delete package.
     */
    public function destroy(Package $package)
    {
        $this->authorize('delete', $package);

        if ($package->cover_image_path) {
            $oldPath = str_replace('storage/', '', $package->cover_image_path);
            Storage::disk('public')->delete($oldPath);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Paket sewa berhasil dihapus.');
    }
}
