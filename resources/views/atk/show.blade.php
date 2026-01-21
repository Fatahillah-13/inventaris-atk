<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Detail Permintaan
                </h2>
                <p class="text-xs text-gray-400">{{ $atkShopRequest->request_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('atk.my-requests') }}"
                    class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-white">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Request Info --}}
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-100">Informasi Permintaan</h3>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Nomor Permintaan</p>
                        <p class="text-sm font-semibold text-slate-100">{{ $atkShopRequest->request_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Status</p>
                        @if ($atkShopRequest->status === 'submitted')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-900/30 text-blue-300 border border-blue-700/50">
                                Submitted
                            </span>
                        @elseif ($atkShopRequest->status === 'waiting_list')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-emerald-900/30 text-emerald-300 border border-emerald-700/50">
                                Waiting List
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-slate-700/30 text-slate-300 border border-slate-600/50">
                                {{ ucfirst($atkShopRequest->status) }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Periode</p>
                        <p class="text-sm text-slate-200">{{ \Carbon\Carbon::createFromFormat('Y-m', $atkShopRequest->period)->format('F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Tanggal Diajukan</p>
                        <p class="text-sm text-slate-200">{{ $atkShopRequest->submitted_at->format('d F Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Peminta</p>
                        <p class="text-sm text-slate-200">{{ $atkShopRequest->requestedBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Divisi</p>
                        <p class="text-sm text-slate-200">{{ $atkShopRequest->division->nama ?? '-' }}</p>
                    </div>
                    @if ($atkShopRequest->approved_at)
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Disetujui Oleh</p>
                            <p class="text-sm text-slate-200">{{ $atkShopRequest->approvedBy->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Tanggal Disetujui</p>
                            <p class="text-sm text-slate-200">{{ $atkShopRequest->approved_at->format('d F Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rejection Information (if rejected) --}}
            @if ($atkShopRequest->rejection_reason)
                <div class="bg-amber-900/20 border border-amber-500/50 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-amber-700/50">
                        <h3 class="text-sm font-semibold text-amber-200">Informasi Penolakan</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="text-xs text-amber-300/70 mb-1">Ditolak Oleh</p>
                            <p class="text-sm text-amber-100">{{ $atkShopRequest->rejectedBy->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-amber-300/70 mb-1">Tanggal Ditolak</p>
                            <p class="text-sm text-amber-100">{{ $atkShopRequest->rejected_at?->format('d F Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-amber-300/70 mb-1">Alasan Penolakan</p>
                            <p class="text-sm text-amber-100">{{ $atkShopRequest->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Items List --}}
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-100">Daftar Item</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-800/50 border-b border-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Kode Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-300">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($atkShopRequest->items as $index => $requestItem)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-4 py-3 text-slate-300">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-slate-100">{{ $requestItem->item->kode_barang }}</td>
                                    <td class="px-4 py-3 text-slate-100">{{ $requestItem->item->nama_barang }}</td>
                                    <td class="px-4 py-3 text-slate-300">{{ $requestItem->item->satuan }}</td>
                                    <td class="px-4 py-3 text-right text-slate-100 font-medium">{{ $requestItem->qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-800/50 border-t border-slate-700">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-xs font-semibold text-slate-300">Total Item:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-slate-100">{{ $atkShopRequest->items->count() }} item</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-xs font-semibold text-slate-300">Total Jumlah:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-slate-100">{{ $atkShopRequest->items->sum('qty') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
