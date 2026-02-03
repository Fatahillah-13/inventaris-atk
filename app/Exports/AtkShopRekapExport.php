<?php

namespace App\Exports;

use App\Models\AtkShopRequestItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AtkShopRekapExport implements FromCollection, WithHeadings
{

    protected $period;
    public function __construct($period)
    {
        $this->period = $period;
    }

    public function collection()
    {
        // Ambil rekap per item
        $rekap = AtkShopRequestItem::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereHas('atkShopRequest', function ($q) {
                $q->where('period', $this->period)
                    ->where('status', 'waiting_list');
            })
            ->groupBy('item_id')
            ->with('item')
            ->get()
            ->map(function ($row, $idx) {
                return [
                    'No' => $idx + 1,
                    'Nama Barang' => $row->item->nama_barang,
                    'Total Qty' => $row->total_qty,
                ];
            });

        return collect($rekap);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Total Qty',
        ];
    }
}
