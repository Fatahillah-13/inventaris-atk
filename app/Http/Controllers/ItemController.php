<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\ItemCategory;

class ItemController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:admin,staff_pengelola']);
    // }

    public function index(Request $request)
    {
        $query = Item::with(['divisionStocks.division']); // penting: eager load

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
            'kode_barang'   => 'required|unique:items,kode_barang',
            'nama_barang'   => 'required',
            'category_id'   => 'nullable|exists:item_categories,id',
            'satuan'        => 'required',
            'catatan'       => 'nullable|string',
            'can_be_loaned' => 'nullable|boolean',
        ]);

        $item = Item::create([
            'kode_barang'   => $validated['kode_barang'],
            'nama_barang'   => $validated['nama_barang'],
            'category_id'   => $validated['category_id'],
            // kalau kamu masih ingin isi item_category string:
            'item_category' => ItemCategory::find($validated['category_id'])->kode ?? null,
            'satuan'        => $validated['satuan'],
            'stok_awal'     => 0,
            'stok_terkini'  => 0,
            'catatan'       => $validated['catatan'] ?? null,
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
            'kode_barang'   => 'required|unique:items,kode_barang,' . $item->id,
            'nama_barang'   => 'required',
            'category_id'   => 'nullable|exists:item_categories,id',
            'satuan'        => 'required',
            // 'stok_awal'     => 'required|integer|min:0',
            'catatan'       => 'nullable|string',
            'can_be_loaned' => 'nullable|boolean',
        ]);

        $item->update([
            'kode_barang'   => $validated['kode_barang'],
            'nama_barang'   => $validated['nama_barang'],
            'category_id'   => $validated['category_id'],
            'item_category' => ItemCategory::find($validated['category_id'])->kode ?? $item->item_category,
            'satuan'        => $validated['satuan'],
            'catatan'       => $validated['catatan'] ?? null,
            'can_be_loaned' => $request->has('can_be_loaned'),
        ]);

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
}
