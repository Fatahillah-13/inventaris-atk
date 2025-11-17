<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Division;
use App\Models\ItemDivisionStock;
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

    public function index(Request $request)
    {
        $query = StockMovement::with('item', 'user');

        // Search: kode barang, nama barang, atau keterangan
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($qi) use ($search) {
                    $qi->where('kode_barang', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%");
                })->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Filter jenis: masuk / keluar
        if ($request->filled('jenis') && in_array($request->jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $request->jenis);
        }

        // Urutan waktu: terbaru / terlama
        $waktu = $request->get('waktu', 'terbaru'); // default: terbaru
        $direction = $waktu === 'terlama' ? 'asc' : 'desc';

        $query->orderBy('tanggal', $direction)
            ->orderBy('created_at', $direction);

        $movements = $query->paginate(20)->withQueryString();

        return view('stock.index', [
            'movements' => $movements,
            'filters'   => [
                'q'     => $request->q,
                'jenis' => $request->jenis,
                'waktu' => $waktu,
            ],
        ]);
    }

    public function createMasuk()
    {
        $items = Item::orderBy('nama_barang')->get();
        $divisions = Division::orderBy('nama')->get();
        return view('stock.masuk', compact('items', 'divisions'));
    }

    public function storeMasuk(Request $request)
    {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'division_id' => 'required|exists:divisions,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $stock = ItemDivisionStock::lockForUpdate()
                ->firstOrCreate([
                    'item_id'     => $validated['item_id'],
                    'division_id' => $validated['division_id'],
                ], [
                    'stok_terkini' => 0,
                ]);

            // tambahkan stok
            $stock->increment('stok_terkini', $validated['jumlah']);

            // catat pergerakan stok
            StockMovement::create([
                'item_id'     => $validated['item_id'],
                'division_id' => $validated['division_id'],
                'jenis'       => 'masuk',
                'jumlah'      => $validated['jumlah'],
                'tanggal'     => $validated['tanggal'],
                'user_id'     => Auth::id(),
                'keterangan'  => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function createKeluar()
    {
        $items = Item::orderBy('nama_barang')->get();
        $divisions = Division::orderBy('nama')->get();
        return view('stock.keluar', compact('items', 'divisions'));
    }

    public function storeKeluar(Request $request)
    {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'division_id' => 'required|exists:divisions,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $stock = ItemDivisionStock::lockForUpdate()
                ->where('item_id', $validated['item_id'])
                ->where('division_id', $validated['division_id'])
                ->first();

            if (!$stock || $stock->stok_terkini < $validated['jumlah']) {
                abort(400, 'Stok pada divisi ini tidak mencukupi.');
            }

            $stock->decrement('stok_terkini', $validated['jumlah']);

            StockMovement::create([
                'item_id'     => $validated['item_id'],
                'division_id' => $validated['division_id'],
                'jenis'       => 'keluar',
                'jumlah'      => $validated['jumlah'],
                'tanggal'     => $validated['tanggal'],
                'user_id'     => Auth::id(),
                'keterangan'  => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Barang keluar berhasil dicatat.');
    }
}
