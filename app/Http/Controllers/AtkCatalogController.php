<?php

namespace App\Http\Controllers;

use App\Models\AtkShopRequest;
use App\Models\AtkShopRequestItem;
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
            ->paginate(18); // 18 items per page (3 columns x 6 rows)

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

        // Get user's division_id (optional field)
        $divisionId = Auth::user()->division_id ?? null;

        DB::transaction(function () use ($validated, $period, $userId, $divisionId) {
            // Find or create draft request for this user+period
            $atkShopRequest = AtkShopRequest::firstOrCreate(
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
            $requestItem = AtkShopRequestItem::where('atk_shop_request_id', $atkShopRequest->id)
                ->where('item_id', $validated['item_id'])
                ->first();

            if ($requestItem) {
                // Update quantity (add to existing)
                $requestItem->increment('qty', $validated['qty']);
            } else {
                // Create new request item
                AtkShopRequestItem::create([
                    'atk_shop_request_id' => $atkShopRequest->id,
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

        $atkShopRequest = AtkShopRequest::with(['items.item'])
            ->where('requested_by', $userId)
            ->where('period', $period)
            ->where('status', 'draft')
            ->first();

        return view('atk.cart', compact('atkShopRequest'));
    }

    /**
     * Update quantity of cart item
     */
    public function updateCartItem(Request $request, AtkShopRequestItem $atkShopRequestItem)
    {
        // Ensure the item belongs to user's draft
        if ($atkShopRequestItem->atkShopRequest->requested_by !== Auth::id() ||
            $atkShopRequestItem->atkShopRequest->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $atkShopRequestItem->update(['qty' => $validated['qty']]);

        return redirect()
            ->route('atk.cart')
            ->with('success', 'Jumlah item berhasil diperbarui.');
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(AtkShopRequestItem $atkShopRequestItem)
    {
        // Ensure the item belongs to user's draft
        if ($atkShopRequestItem->atkShopRequest->requested_by !== Auth::id() ||
            $atkShopRequestItem->atkShopRequest->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $atkShopRequestItem->delete();

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

        $atkShopRequest = AtkShopRequest::with('items')
            ->where('requested_by', $userId)
            ->where('period', $period)
            ->where('status', 'draft')
            ->first();

        if (!$atkShopRequest || $atkShopRequest->items->isEmpty()) {
            return redirect()
                ->route('atk.cart')
                ->with('error', 'Keranjang kosong. Tambahkan item terlebih dahulu.');
        }

        DB::transaction(function () use ($atkShopRequest, $period) {
            // Generate request number with database locking
            $yearMonth = now()->format('Ym');
            
            // Lock the table to prevent race conditions
            $lastRequest = AtkShopRequest::where('request_number', 'like', "REQ-{$yearMonth}%")
                ->lockForUpdate()
                ->orderBy('request_number', 'desc')
                ->first();

            $sequence = 1;
            if ($lastRequest) {
                $lastSequence = (int) substr($lastRequest->request_number, -4);
                $sequence = $lastSequence + 1;
            }

            $requestNumber = sprintf('REQ-%s-%04d', $yearMonth, $sequence);

            // Update request status
            $atkShopRequest->update([
                'request_number' => $requestNumber,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        });

        return redirect()
            ->route('atk.my-requests')
            ->with('success', 'Permintaan berhasil diajukan dengan nomor: ' . $atkShopRequest->request_number);
    }

    /**
     * List user's submitted requests
     */
    public function myRequests()
    {
        $requests = AtkShopRequest::with(['items.item', 'division'])
            ->where('requested_by', Auth::id())
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        return view('atk.my-requests', compact('requests'));
    }

    /**
     * Show request detail
     */
    public function showRequest(AtkShopRequest $atkShopRequest)
    {
        // Ensure user can only view their own requests
        if ($atkShopRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $atkShopRequest->load(['items.item', 'division', 'requestedBy']);

        return view('atk.show', compact('atkShopRequest'));
    }
}
