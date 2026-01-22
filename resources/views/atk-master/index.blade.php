<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Persetujuan Permintaan ATK
                </h2>
                <p class="text-xs text-gray-400">Daftar permintaan ATK yang menunggu persetujuan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-emerald-900/30 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($requests->isEmpty())
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-8 text-center">
                    <p class="text-slate-400">Tidak ada permintaan yang menunggu persetujuan.</p>
                </div>
            @else
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                        <p class="text-xs text-slate-400">
                            Menampilkan {{ $requests->firstItem() }}–{{ $requests->lastItem() }} dari {{ $requests->total() }} permintaan
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-800/50 border-b border-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">No. Permintaan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Peminta</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Divisi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Periode</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Tanggal Ajukan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Total Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($requests as $request)
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="px-4 py-3 text-slate-100 font-medium">{{ $request->request_number }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $request->requestedBy->name }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $request->division->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ \Carbon\Carbon::createFromFormat('Y-m', $request->period)->format('F Y') }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $request->submitted_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $request->items->count() }} item ({{ $request->items->sum('qty') }} total)</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('atk-master.show', $request) }}"
                                                class="text-emerald-400 hover:text-emerald-300 font-medium">
                                                Lihat Detail →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($requests->hasPages())
                        <div class="px-4 py-3 border-t border-slate-800">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
