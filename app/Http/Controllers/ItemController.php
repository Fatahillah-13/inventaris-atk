<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:admin,staff_pengelola']);
    // }

    public function index(Request $request)
    {
        $query = Item::query();

        if ($search = $request->input('q')) {
            $query->where('kode_barang', 'like', "%$search%")
                ->orWhere('nama_barang', 'like', "%$search%");
        }

        $items = $query->orderBy('nama_barang')->paginate(15);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang'   => 'required|unique:items,kode_barang',
            'nama_barang'   => 'required',
            'item_category' => 'nullable|string',
            'satuan'        => 'required',
            'stok_awal'     => 'required|integer|min:0',
            'catatan'       => 'nullable|string',
        ]);

        $validated['stok_terkini'] = $validated['stok_awal'];

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'kode_barang'   => 'required|unique:items,kode_barang,' . $item->id,
            'nama_barang'   => 'required',
            'item_category' => 'nullable|string',
            'satuan'        => 'required',
            // 'stok_awal'     => 'required|integer|min:0',
            'catatan'       => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
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
