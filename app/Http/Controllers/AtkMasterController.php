<?php

namespace App\Http\Controllers;

use App\Models\AtkShopRequest;
use App\Models\ItemDivisionStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AtkMasterController extends Controller
{
    /**
     * Display list of submitted requests for approval
     */
    public function index(Request $request)
    {
        $query = AtkShopRequest::with(['requestedBy', 'division', 'items.item'])
            ->orderBy('submitted_at', 'desc');

        // Optional filter by period
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('atk-master.index', compact('requests'));
    }

    public function requestList(Request $request)
    {
        $query = AtkShopRequest::with(['requestedBy', 'division', 'items.item'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc');

        // Optional filter by period
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('atk-master.requestList', compact('requests'));
    }

    /**
     * Show request detail for approval
     */
    public function show(AtkShopRequest $atkShopRequest)
    {
        // Only show submitted requests
        // if ($atkShopRequest->status !== 'submitted') {
        //     return redirect()
        //         ->route('atk-master.index')
        //         ->with('error', 'Permintaan ini tidak dalam status submitted.');
        // }

        $atkShopRequest->load(['items.item', 'division', 'requestedBy']);

        return view('atk-master.show', compact('atkShopRequest'));
    }

    /**
     * Approve a request - change status from submitted to waiting_list
     */
    public function approve(AtkShopRequest $atkShopRequest)
    {
        // Validate status
        if ($atkShopRequest->status !== 'submitted') {
            return redirect()
                ->route('atk-master.index')
                ->with('error', 'Permintaan ini tidak dapat disetujui. Status saat ini: '.$atkShopRequest->status);
        }

        // Update status to waiting_list
        $atkShopRequest->update([
            'status' => 'waiting_list',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('atk-master.index')
            ->with('success', 'Permintaan '.$atkShopRequest->request_number.' telah disetujui dan dipindahkan ke daftar tunggu.');
    }

    /**
     * Reject a request - change status back to draft with reason
     */
    public function reject(Request $request, AtkShopRequest $atkShopRequest)
    {
        // Validate status
        if ($atkShopRequest->status !== 'submitted') {
            return redirect()
                ->route('atk-master.index')
                ->with('error', 'Permintaan ini tidak dapat ditolak. Status saat ini: '.$atkShopRequest->status);
        }

        // Validate rejection reason
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        // Update status to draft and clear submission data
        $atkShopRequest->update([
            'status' => 'draft',
            'request_number' => null,
            'submitted_at' => null,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('atk-master.index')
            ->with('success', 'Permintaan telah ditolak dan dikembalikan ke peminta untuk diperbaiki.');
    }

    public function readytoPickup(AtkShopRequest $atkShopRequest)
    {
        // Pastikan status sudah approved sebelum berubah ke waiting_list
        if ($atkShopRequest->status !== 'waiting_list') {
            return back()->with('error', 'Tunggu persetujuan');
        }

        // Update status ke waiting_list
        $atkShopRequest->update([
            'status' => 'ready_to_pickup',
            'waiting_list_at' => now(),
        ]);

        return redirect()->route('atk-master.index')->with('success', 'Barang telah datang. harap menunggu informasi pengambilan.');
    }

    public function finish(Request $request, AtkShopRequest $atkShopRequest)
    {
        // Pastikan status sudah waiting_list/approved sebelum berubah ke done
        if ($atkShopRequest->status !== 'ready_to_pickup') {
            return back()->with('error', 'Permintaan hanya dapat diselesaikan setelah barang tersedia diambil.');
        }

        DB::beginTransaction();
        try {
            // Update status ke selesai/done
            $atkShopRequest->update([
                'status' => 'done',
                'updated_at' => now(),
            ]);

            $atkShopRequest->loadMissing(['items.item', 'division']);

            $tanggal = now()->toDateString();
            $ref = $atkShopRequest->request_number ?: ('ATKSHOP-' . $atkShopRequest->id);

            // Untuk setiap item di permintaan, tambahkan stok & catat stock movement (MASUK)
            foreach ($atkShopRequest->items as $itemDetail) {
                $qty = (int) $itemDetail->qty;
                if ($qty <= 0) {
                    continue;
                }

                $item = $itemDetail->item;

                if ($atkShopRequest->division_id) {
                    $divisionStock = ItemDivisionStock::lockForUpdate()
                        ->where('item_id', $item->id)
                        ->where('division_id', $atkShopRequest->division_id)
                        ->first();

                    if (! $divisionStock) {
                        $divisionStock = ItemDivisionStock::create([
                            'item_id' => $item->id,
                            'division_id' => $atkShopRequest->division_id,
                            'stok_terkini' => 0,
                        ]);
                    }

                    $divisionStock->increment('stok_terkini', $qty);
                } else {
                    $item->increment('stok_terkini', $qty);
                }

                StockMovement::create([
                    'item_id' => $item->id,
                    'division_id' => $atkShopRequest->division_id,
                    'jenis' => 'masuk',
                    'jumlah' => $qty,
                    'tanggal' => $tanggal,
                    'user_id' => Auth::id(),
                    'keterangan' => 'Pengadaan ATK ' . $ref . ' (periode ' . $atkShopRequest->period . ') selesai - stok masuk',
                ]);
            }

            DB::commit();

            return redirect()->route('atk-master.index')->with('success', 'Permintaan selesai & stok berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal update stok: '.$e->getMessage());
        }
    }
}
