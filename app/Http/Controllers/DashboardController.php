<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\AtkRequest;        // <-- SESUAIKAN: kalau model permintaan ATK kamu namanya beda, ubah ini
use App\Models\ItemDivisionStock;  // <-- Model stok per divisi
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Total barang (master item)
        $totalItems = Item::count();

        // Peminjaman yang masih aktif (status 'dipinjam')
        $activeLoans = Loan::where('status', 'dipinjam')->count();

        // Permintaan ATK yang dibuat hari ini (pakai created_at)
        $todayRequests = AtkRequest::whereDate('created_at', Carbon::today())->count();

        // Stok menipis: misal jika stok per divisi <= 3
        // (kalau maunya per item, nanti bisa kita ubah)
        $threshold = 3;
        $lowStocks = ItemDivisionStock::where('stok_terkini', '<=', $threshold)->count();

        // Aktivitas terbaru: ambil 10 pergerakan stok terakhir
        $recentMovements = StockMovement::with(['item', 'division', 'user'])
            ->orderByDesc('tanggal')    // kalau field tanggal ada
            ->orderByDesc('created_at') // fallback kalau tanggal sama
            ->limit(3)
            ->get();

        return view('dashboard', [
            'totalItems'       => $totalItems,
            'activeLoans'      => $activeLoans,
            'todayRequests'    => $todayRequests,
            'lowStocks'        => $lowStocks,
            'recentMovements'  => $recentMovements,
        ]);
    }
}
