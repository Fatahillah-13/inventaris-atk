<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemCategory;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::orderBy('kode')->paginate(20);

        return view('item_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('item_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:20|unique:item_categories,kode',
            'nama'      => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        ItemCategory::create([
            'kode'      => strtoupper($validated['kode']),
            'nama'      => $validated['nama'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('item-categories.index')
            ->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function edit(ItemCategory $itemCategory)
    {
        return view('item_categories.edit', [
            'category' => $itemCategory,
        ]);
    }

    public function update(Request $request, ItemCategory $itemCategory)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:20|unique:item_categories,kode,' . $itemCategory->id,
            'nama'      => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $itemCategory->update([
            'kode'      => strtoupper($validated['kode']),
            'nama'      => $validated['nama'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('item-categories.index')
            ->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(ItemCategory $itemCategory)
    {
        $itemCategory->delete();

        return redirect()
            ->route('item-categories.index')
            ->with('success', 'Kategori barang berhasil dihapus.');
    }
}
