<?php

namespace App\Http\Controllers;

use App\Models\AtkRequest;
use App\Models\Item;
use App\Models\Division;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AtkRequestController extends Controller
{
    // === FORM PUBLIK (TANPA LOGIN) ===

    public function publicCreate()
    {
        // tampilkan semua barang (atau bisa difilter stok > 0 kalau mau)
        $items = Item::orderBy('nama_barang')->get();
        $divisions = Division::orderBy('nama')->get();

        return view('requests.public_create', compact('items', 'divisions'));
    }

    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'peminta'    => 'required|string|max:100',
            'departemen' => 'required|string|max:100',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // kunci barang
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if ($item->stok_terkini < $validated['jumlah']) {
                abort(400, 'Stok tidak mencukupi untuk permintaan ini.');
            }

            // kurangi stok
            $item->decrement('stok_terkini', $validated['jumlah']);

            // kode permintaan
            $kode = 'REQ-' . now()->format('YmdHis');

            // user internal (kalau form ini diisi staf yang login), kalau public biasa -> null
            $userId = Auth::check() ? Auth::id() : null;

            // simpan permintaan
            $req = AtkRequest::create([
                'kode_request' => $kode,
                'item_id'      => $item->id,
                'user_id'      => $userId,
                'peminta'      => $validated['peminta'],
                'departemen'   => $validated['departemen'],
                'jumlah'       => $validated['jumlah'],
                'tanggal'      => $validated['tanggal'],
                'keterangan'   => $validated['keterangan'] ?? null,
            ]);

            // catat pergerakan stok: BARANG KELUAR
            StockMovement::create([
                'item_id'    => $item->id,
                'jenis'      => 'keluar',
                'jumlah'     => $validated['jumlah'],
                'tanggal'    => $validated['tanggal'],
                'user_id'    => $userId,
                'keterangan' => 'Permintaan ATK '
                    . $req->kode_request . ' oleh '
                    . $req->peminta . ' (' . $req->departemen . ')',
            ]);
        });

        return redirect()
            ->route('public.requests.create')
            ->with('success', 'Permintaan ATK Anda telah dicatat. Silakan ambil barang di bagian ATK.');
    }

    // === INTERNAL: DAFTAR PERMINTAAN (ADMIN & STAFF PENGELOLA) ===
    public function index(Request $request)
    {
        $query = AtkRequest::with('item', 'user');

        // pencarian sederhana: peminta, departemen, kode/nama barang
        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->where('peminta', 'like', "%{$search}%")
                    ->orWhere('departemen', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($qi) use ($search) {
                        $qi->where('kode_barang', 'like', "%{$search}%")
                            ->orWhere('nama_barang', 'like', "%{$search}%");
                    });
            });
        }

        // urutkan terbaru dulu
        $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        $requests = $query->paginate(20)->withQueryString();

        return view('requests.index', [
            'requests' => $requests,
            'q'        => $request->q,
        ]);
    }
}
