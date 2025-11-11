<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:admin,staff_pengelola']);
    // }

    public function index()
    {
        $movements = StockMovement::with('item', 'user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('stock.index', compact('movements'));
    }

    public function createMasuk()
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('stock.masuk', compact('items'));
    }

    public function storeMasuk(Request $request)
    {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            $item->increment('stok_terkini', $validated['jumlah']);

            StockMovement::create([
                'item_id'    => $item->id,
                'jenis'      => 'masuk',
                'jumlah'     => $validated['jumlah'],
                'tanggal'    => $validated['tanggal'],
                'user_id'    => Auth::id(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function createKeluar()
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('stock.keluar', compact('items'));
    }

    public function storeKeluar(Request $request)
    {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if ($item->stok_terkini < $validated['jumlah']) {
                abort(400, 'Stok tidak mencukupi.');
            }

            $item->decrement('stok_terkini', $validated['jumlah']);

            StockMovement::create([
                'item_id'    => $item->id,
                'jenis'      => 'keluar',
                'jumlah'     => $validated['jumlah'],
                'tanggal'    => $validated['tanggal'],
                'user_id'    => Auth::id(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Barang keluar berhasil dicatat.');
    }
}
