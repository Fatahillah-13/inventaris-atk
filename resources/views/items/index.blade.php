<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Master Barang
                </h2>
                <p class="text-xs text-gray-400">Data inventaris & stok per divisi</p>
            </div>
            <a href="{{ route('items.create') }}"
                class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                + Tambah Barang
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Search --}}
            <form method="GET" action="{{ route('items.index') }}"
                class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 mb-2">
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <div class="flex-1">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari kode / nama / kategori barang..."
                            class="w-full rounded-md border-slate-700 bg-slate-900 text-sm text-slate-100 placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <button
                        class="px-4 py-2 rounded-md bg-emerald-500 text-xs font-semibold text-white hover:bg-emerald-600">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-400">
                        Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} barang
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="bg-slate-900/80 text-slate-300 border-b border-slate-800 text-xs uppercase tracking-wide">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Kategori</th>
                                <th class="px-3 py-2 text-left">Satuan</th>
                                <th class="px-3 py-2 text-right">Stok Total</th>
                                <th class="px-3 py-2 text-left">Stok per Divisi</th>
                                <th class="px-3 py-2 text-center">Pinjam?</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($items as $item)
                                @php
                                    $totalStok = $item->divisionStocks->sum('stok_terkini');
                                @endphp
                                <tr class="hover:bg-slate-800/70">
                                    <td class="px-3 py-2 text-slate-100 font-semibold">{{ $item->kode_barang }}</td>
                                    <td class="px-3 py-2 text-slate-50">{{ $item->nama_barang }}</td>
                                    <td class="px-3 py-2 text-slate-300">{{ $item->item_category ?? '-' }}</td>
                                    <td class="px-3 py-2 text-slate-300">{{ $item->satuan }}</td>
                                    <td class="px-3 py-2 text-right text-slate-50 font-semibold">{{ $totalStok }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($item->divisionStocks->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($item->divisionStocks as $ds)
                                                    @if ($ds->division)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-slate-800 text-slate-200 border border-slate-700">
                                                            {{ $ds->division->nama }}:
                                                            <span
                                                                class="ml-1 font-semibold">{{ $ds->stok_terkini }}</span>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-500 italic">Belum ada stok per divisi</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        @if ($item->can_be_loaned)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[11px] bg-emerald-500/15 text-emerald-300 border border-emerald-500/40">
                                                Bisa
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[11px] bg-slate-800 text-slate-400 border border-slate-700">
                                                Tidak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="inline-flex gap-3 text-xs">
                                            <a href="{{ route('items.edit', $item) }}"
                                                class="text-emerald-300 hover:text-emerald-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-center text-sm text-slate-400">
                                        Belum ada data barang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-slate-800">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
