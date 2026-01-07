<?php

namespace App\Http\Controllers;

use App\Models\AtkRequest;
use App\Models\AtkRequestItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AtkCatalogController extends Controller
{
    /**
     * Display catalog of requestable items
     */
    public function catalog()
    {
        $items = Item::where('is_requestable', true)
            ->orderBy('nama_barang')
            ->get();

        return view('atk.catalog', compact('items'));
    }

    /**
     * Add item to cart (create or find draft for current period)
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|integer|min:1',
        ]);

        // Verify item is requestable
        $item = Item::findOrFail($validated['item_id']);
        if (!$item->is_requestable) {
            return redirect()
                ->back()
                ->with('error', 'Item ini tidak dapat diminta.');
        }

        $period = now()->format('Y-m');
        $userId = Auth::id();

        // Get user's division_id (assuming user has division_id)
        $divisionId = Auth::user()->division_id ?? null;

        DB::transaction(function () use ($validated, $period, $userId, $divisionId) {
            // Find or create draft request for this user+period
            $atkRequest = AtkRequest::firstOrCreate(
                [
                    'requested_by' => $userId,
                    'period' => $period,
                    'status' => 'draft',
                ],
                [
                    'division_id' => $divisionId,
                ]
            );

            // Check if item already in cart
            $requestItem = AtkRequestItem::where('atk_request_id', $atkRequest->id)
                ->where('item_id', $validated['item_id'])
                ->first();

            if ($requestItem) {
                // Update quantity (add to existing)
                $requestItem->increment('qty', $validated['qty']);
            } else {
                // Create new request item
                AtkRequestItem::create([
                    'atk_request_id' => $atkRequest->id,
                    'item_id' => $validated['item_id'],
                    'qty' => $validated['qty'],
                ]);
            }
        });

        return redirect()
            ->route('atk.cart')
            ->with('success', 'Item berhasil ditambahkan ke keranjang.');
    }

    /**
     * View current cart (draft request)
     */
    public function viewCart()
    {
        $period = now()->format('Y-m');
        $userId = Auth::id();

        $atkRequest = AtkRequest::with(['items.item'])
            ->where('requested_by', $userId)
            ->where('period', $period)
            ->where('status', 'draft')
            ->first();

        return view('atk.cart', compact('atkRequest'));
    }

    /**
     * Update quantity of cart item
     */
    public function updateCartItem(Request $request, AtkRequestItem $atkRequestItem)
    {
        // Ensure the item belongs to user's draft
        if ($atkRequestItem->atkRequest->requested_by !== Auth::id() ||
            $atkRequestItem->atkRequest->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $atkRequestItem->update(['qty' => $validated['qty']]);

        return redirect()
            ->route('atk.cart')
            ->with('success', 'Jumlah item berhasil diperbarui.');
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(AtkRequestItem $atkRequestItem)
    {
        // Ensure the item belongs to user's draft
        if ($atkRequestItem->atkRequest->requested_by !== Auth::id() ||
            $atkRequestItem->atkRequest->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $atkRequestItem->delete();

        return redirect()
            ->route('atk.cart')
            ->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Checkout - submit the draft request
     */
    public function checkout()
    {
        $period = now()->format('Y-m');
        $userId = Auth::id();

        $atkRequest = AtkRequest::with('items')
            ->where('requested_by', $userId)
            ->where('period', $period)
            ->where('status', 'draft')
            ->first();

        if (!$atkRequest || $atkRequest->items->isEmpty()) {
            return redirect()
                ->route('atk.cart')
                ->with('error', 'Keranjang kosong. Tambahkan item terlebih dahulu.');
        }

        DB::transaction(function () use ($atkRequest, $period) {
            // Generate request number
            $yearMonth = now()->format('Ym');
            $lastRequest = AtkRequest::where('request_number', 'like', "REQ-{$yearMonth}%")
                ->orderBy('request_number', 'desc')
                ->first();

            $sequence = 1;
            if ($lastRequest) {
                $lastSequence = (int) substr($lastRequest->request_number, -4);
                $sequence = $lastSequence + 1;
            }

            $requestNumber = sprintf('REQ-%s-%04d', $yearMonth, $sequence);

            // Update request status
            $atkRequest->update([
                'request_number' => $requestNumber,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        });

        return redirect()
            ->route('atk.my-requests')
            ->with('success', 'Permintaan berhasil diajukan dengan nomor: ' . $atkRequest->request_number);
    }

    /**
     * List user's submitted requests
     */
    public function myRequests()
    {
        $requests = AtkRequest::with(['items.item', 'division'])
            ->where('requested_by', Auth::id())
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        return view('atk.my-requests', compact('requests'));
    }

    /**
     * Show request detail
     */
    public function showRequest(AtkRequest $atkRequest)
    {
        // Ensure user can only view their own requests
        if ($atkRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $atkRequest->load(['items.item', 'division', 'requestedBy']);

        return view('atk.show', compact('atkRequest'));
    }
}
