<?php

namespace App\Http\Controllers;

use App\Models\AtkRequest;
use App\Models\Item;
use App\Models\Division;
use App\Models\ItemDivisionStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AtkRequestController extends Controller
{
    // === FORM PUBLIK (TANPA LOGIN) ===

    // public function publicCreate()
    // {
    //     // tampilkan semua barang (atau bisa difilter stok > 0 kalau mau)
    //     $items = Item::orderBy('nama_barang')->get();
    //     $divisions = Division::orderBy('nama')->get();

    //     return view('requests.public_create', compact('items', 'divisions'));
    // }

    // public function publicStore(Request $request)
    // {
    //     $validated = $request->validate([
    //         'item_id'    => 'required|exists:items,id',
    //         'peminta'    => 'required|string|max:100',
    //         'division_id' => 'required|exists:divisions,id',
    //         'jumlah'     => 'required|integer|min:1',
    //         'tanggal'    => 'required|date',
    //         'keterangan' => 'nullable|string|max:255',
    //     ]);

    //     // kode permintaan
    //     $kode = 'REQ-' . now()->format('YmdHis');

    //     // user internal (kalau form ini diisi staf yang login), kalau public biasa -> null
    //     $userId = Auth::check() ? Auth::id() : null;

    //     // simpan permintaan
    //     AtkRequest::create([
    //         'kode_request' => $kode,
    //         'item_id'      => $validated['item_id'],
    //         'user_id'      => $userId,
    //         'division_id'  => $validated['division_id'],
    //         'peminta'      => $validated['peminta'],
    //         'departemen'   => Division::find($validated['division_id'])->nama,
    //         'jumlah'       => $validated['jumlah'],
    //         'tanggal'      => $validated['tanggal'],
    //         'keterangan'   => $validated['keterangan'] ?? null,
    //         'status'      => 'pending',
    //         'approved_by' => null,
    //         'approved_at' => null,
    //     ]);

    //     return redirect()
    //         ->route('public.requests.create')
    //         ->with('success', 'Permintaan ATK Anda telah dicatat. Silakan ambil barang di bagian ATK.');
    // }

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

    public function approve(AtkRequest $atkRequest)
    {

        // hanya boleh approve kalau masih pending
        if ($atkRequest->status !== 'pending') {
            return redirect()
                ->route('requests.index')
                ->with('error', 'Permintaan ini sudah diproses.');
        }

        try {
            DB::transaction(function () use ($atkRequest) {

                // kunci stok item + division
                $stock = ItemDivisionStock::lockForUpdate()
                    ->where('item_id', $atkRequest->item_id)
                    ->where('division_id', $atkRequest->division_id)
                    ->first();

                if (! $stock || $stock->stok_terkini < $atkRequest->jumlah) {
                    // kalau stok tidak cukup, lempar error agar transaksi rollback
                    throw new \RuntimeException(
                        'Stok tidak mencukupi. Stok tersedia: ' . ($stock->stok_terkini ?? 0)
                    );
                }

                // kurangi stok
                $stock->decrement('stok_terkini', $atkRequest->jumlah);

                // catat pergerakan stok: KELUAR karena permintaan ATK
                StockMovement::create([
                    'item_id'     => $atkRequest->item_id,
                    'division_id' => $atkRequest->division_id,
                    'jenis'       => 'keluar',
                    'jumlah'      => $atkRequest->jumlah,
                    'tanggal'     => $atkRequest->tanggal,
                    'user_id'     => Auth::id(), // admin/staff yang approve
                    'keterangan'  => 'Permintaan ATK ' . $atkRequest->kode_request .
                        ' untuk ' . $atkRequest->peminta .
                        ' (' . $atkRequest->departemen . ')',
                ]);

                // update status permintaan
                $atkRequest->update([
                    'status'      => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('requests.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('requests.index')
            ->with('success', 'Permintaan ATK telah disetujui dan stok sudah dikurangi.');
    }

    public function reject(AtkRequest $atkRequest)
    {
        if ($atkRequest->status !== 'pending') {
            return redirect()
                ->route('requests.index')
                ->with('error', 'Permintaan ini sudah diproses.');
        }

        $atkRequest->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('requests.index')
            ->with('success', 'Permintaan ATK telah ditolak. Stok tidak berubah.');
    }
}
