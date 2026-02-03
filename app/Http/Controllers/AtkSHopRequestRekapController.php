<?php

namespace App\Http\Controllers;

use App\Models\AtkShopRequestItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AtkShopRekapExport;

class AtkShopRequestRekapController extends Controller
{
    // Tampilkan rekap di web
    public function index(Request $request)
    {
        
        $period = $request->input('period') ?? now()->format('Y-m');
        // Ambil data request yang sudah di-approve
        $rekap = AtkShopRequestItem::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereHas('atkShopRequest', function ($q) use ($period) {
                $q->where('period', $period)
                    ->where('status', 'waiting_list');
            })
            ->groupBy('item_id')
            ->with('item') // eager load relasi item
            ->get();

        return view('atk-master.rekap', [
            'rekap' => $rekap,
            'period' => $period,
        ]);
    }

    // Export rekapan ke Excel
    public function exportExcel(Request $request)
    {
        $period = $request->input('period') ?? now()->format('Y-m');
        return Excel::download(new AtkShopRekapExport($period), "rekap_atk_{$period}.xlsx");
    }
}
