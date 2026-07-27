<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class AdminItemController extends Controller
{
    /**
     * List all items (paginated) for management.
     */
    public function index()
    {
        $this->authorize('create', Item::class); // Hanya admin

        $items = Item::with('category')
            ->orderBy('call_code', 'asc')
            ->paginate(15);

        return view('admin.items.index', compact('items'));
    }

    /**
     * Form to create a new item.
     */
    public function create()
    {
        $this->authorize('create', Item::class);

        // Fetch categories (exclude 'paket-lengkap' as packages have their own section)
        $categories = Category::where('slug', '!=', 'paket-lengkap')->get();

        return view('admin.items.create', compact('categories'));
    }

    /**
     * Store a newly created item.
     */
    public function store(StoreItemRequest $request)
    {
        $this->authorize('create', Item::class);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('items', 'public');
            $data['cover_image_path'] = 'storage/' . $path;
        }

        Item::create($data);

        return redirect()->route('admin.items.index')->with('success', 'Item berhasil ditambahkan ke katalog.');
    }

    /**
     * Form to edit an existing item.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        $categories = Category::where('slug', '!=', 'paket-lengkap')->get();

        return view('admin.items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $this->authorize('update', $item);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($item->cover_image_path) {
                $oldPath = str_replace('storage/', '', $item->cover_image_path);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('cover_image')->store('items', 'public');
            $data['cover_image_path'] = 'storage/' . $path;
        }

        $item->update($data);

        return redirect()->route('admin.items.index')->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Delete the specified item.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        // Delete cover image from storage
        if ($item->cover_image_path) {
            $oldPath = str_replace('storage/', '', $item->cover_image_path);
            Storage::disk('public')->delete($oldPath);
        }

        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Item berhasil dihapus dari katalog.');
    }
}
