<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Rekap Permintaan ATK Periode {{ $period }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <form method="GET" action="{{ route('atk-master.atkmaster.rekap.excel') }}" class="mb-4 text-right">
                <input type="hidden" name="period" value="{{ $period }}">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 16V20H20V16M12 12V20M12 4V14M8 8L12 12L16 8"></path>
                    </svg>
                    Export Excel
                </button>
            </form>
            <div class="overflow-x-auto bg-slate-900 border border-slate-700/80 rounded-xl shadow">
                <table class="w-full text-sm">
                    <thead class="bg-slate-800/70 border-b border-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Nama Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($rekap as $row)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-4 py-3 text-slate-100 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $row->item->nama_barang ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $row->total_qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-slate-400">Tidak ada data rekap
                                    ATK untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
