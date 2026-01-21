<?php

namespace App\Http\Controllers;

use App\Models\AtkShopRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtkMasterController extends Controller
{
    /**
     * Display list of submitted requests for approval
     */
    public function index(Request $request)
    {
        $query = AtkShopRequest::with(['requestedBy', 'division', 'items.item'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc');

        // Optional filter by period
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('atk-master.index', compact('requests'));
    }

    /**
     * Show request detail for approval
     */
    public function show(AtkShopRequest $atkShopRequest)
    {
        // Only show submitted requests
        if ($atkShopRequest->status !== 'submitted') {
            return redirect()
                ->route('atk-master.index')
                ->with('error', 'Permintaan ini tidak dalam status submitted.');
        }

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
                ->with('error', 'Permintaan ini tidak dapat disetujui. Status saat ini: ' . $atkShopRequest->status);
        }

        // Update status to waiting_list
        $atkShopRequest->update([
            'status' => 'waiting_list',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('atk-master.index')
            ->with('success', 'Permintaan ' . $atkShopRequest->request_number . ' telah disetujui dan dipindahkan ke daftar tunggu.');
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
                ->with('error', 'Permintaan ini tidak dapat ditolak. Status saat ini: ' . $atkShopRequest->status);
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
}

