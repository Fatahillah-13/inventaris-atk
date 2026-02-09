<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:admin,staff_pengelola']);
    // }

    public function index(Request $request)
    {
        // Eager load category to prevent N+1 in view
        $query = Item::with(['divisionStocks.division', 'category']);

        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhere('item_category', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama_barang')->paginate(20)->withQueryString();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = ItemCategory::where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|unique:items,kode_barang',
            'nama_barang' => 'required',
            'category_id' => 'nullable|exists:item_categories,id',
            'satuan' => 'required',
            'catatan' => 'nullable|string',
            'can_be_loaned' => 'nullable|boolean',
        ]);

        $categoryId = $validated['category_id'] ?? null;

        $item = Item::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'category_id' => $categoryId,
            'item_category' => $this->getCategoryCode($categoryId),
            'satuan' => $validated['satuan'],
            'stok_awal' => 0,
            'stok_terkini' => 0,
            'catatan' => $validated['catatan'] ?? null,
            'can_be_loaned' => $request->has('can_be_loaned'),
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = ItemCategory::where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|unique:items,kode_barang,'.$item->id,
            'nama_barang' => 'required',
            'category_id' => 'nullable|exists:item_categories,id',
            'satuan' => 'required',
            // 'stok_awal'     => 'required|integer|min:0',
            'catatan' => 'nullable|string',
            'can_be_loaned' => 'nullable|boolean',
        ]);

        $updateData = [
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'satuan' => $validated['satuan'],
            'catatan' => $validated['catatan'] ?? null,
            'can_be_loaned' => $request->has('can_be_loaned'),
        ];

        // Only update category if it's provided in the request
        if (array_key_exists('category_id', $validated)) {
            $categoryId = $validated['category_id'];
            $updateData['category_id'] = $categoryId;
            $updateData['item_category'] = $this->getCategoryCode($categoryId);
        }

        $item->update($updateData);

        return redirect()->route('items.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    /**
     * Get category code from category ID
     */
    private function getCategoryCode(?int $categoryId): ?string
    {
        if (!$categoryId) {
            return null;
        }
        
        $category = ItemCategory::find($categoryId);
        return $category?->kode;
    }
}
