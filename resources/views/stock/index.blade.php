{{-- resources/views/stock/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            Riwayat Stok ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash message --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card utama --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Header card: judul + tombol --}}
                <div
                    class="px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Histori Pergerakan Stok
                        </h3>
                        <p class="text-xs text-gray-500">
                            Daftar barang masuk dan keluar yang tercatat di sistem inventaris ATK.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('items.index') }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                            + Master Barang
                        </a>
                        <a href="{{ route('stock.masuk.create') }}"
                            class="inline-flex items-center px-3 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                            + Barang Masuk
                        </a>
                        <a href="{{ route('stock.keluar.create') }}"
                            class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            + Barang Keluar
                        </a>
                    </div>
                </div>

                {{-- (Opsional) Deskripsi singkat --}}
                {{-- Deskripsi + Filter --}}
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <p class="text-xs text-gray-500 mb-3">
                        Gunakan filter di bawah untuk mencari transaksi berdasarkan barang, jenis, atau urutan waktu.
                    </p>

                    <form method="GET" action="{{ route('stock.index') }}"
                        class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Search --}}
                        <div class="flex-1 flex gap-2">
                            <label for="q" class="sr-only">Pencarian</label>
                            <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Cari kode / nama barang / keterangan"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            {{-- Filter Jenis & Waktu --}}
                            <div class="flex flex-col sm:flex-row gap-2 sm:items-center mt-2 sm:mt-0">

                                {{-- Jenis --}}
                                <div class="flex items-center gap-1">
                                    <label for="jenis" class="text-xs text-gray-600">Jenis</label>
                                    <select name="jenis" id="jenis"
                                        class="rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        @php $jenis = $filters['jenis'] ?? ''; @endphp
                                        <option value="">Semua</option>
                                        <option value="masuk" {{ $jenis === 'masuk' ? 'selected' : '' }}>Masuk</option>
                                        <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Keluar
                                        </option>
                                    </select>
                                </div>

                                {{-- Waktu --}}
                                <div class="flex items-center gap-1">
                                    <label for="waktu" class="text-xs text-gray-600">Waktu</label>
                                    @php $waktu = $filters['waktu'] ?? 'terbaru'; @endphp
                                    <select name="waktu" id="waktu"
                                        class="rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="terbaru" {{ $waktu === 'terbaru' ? 'selected' : '' }}>Terbaru
                                            dulu
                                        </option>
                                        <option value="terlama" {{ $waktu === 'terlama' ? 'selected' : '' }}>Terlama
                                            dulu
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Tanggal</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Jenis</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Barang</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Divisi</th>
                                <th
                                    class="px-4 py-2 border-b text-right text-xs font-semibold uppercase tracking-wider">
                                    Jumlah</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    User</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($movements as $movement)
                                <tr class="hover:bg-gray-50">
                                    {{-- Tanggal --}}
                                    <td class="px-4 py-2 border-b align-top text-gray-800 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($movement->tanggal)->format('d-m-Y') }}
                                    </td>

                                    {{-- Jenis --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($movement->jenis === 'masuk')
                                            <span
                                                class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Masuk
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Keluar
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Barang --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($movement->item)
                                            <div class="font-semibold text-gray-800">
                                                {{ $movement->item->nama_barang }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Kode: {{ $movement->item->kode_barang }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                Barang tidak ditemukan (mungkin sudah dihapus)
                                            </span>
                                        @endif
                                    </td>
                                    {{-- Divisi --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($movement->division)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[11px] bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $movement->division->nama }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    {{-- Jumlah --}}
                                    <td class="px-4 py-2 border-b align-top text-right">
                                        <span class="font-semibold text-gray-900">
                                            {{ $movement->jumlah }}
                                        </span>
                                        @if ($movement->item)
                                            <span class="text-xs text-gray-500">
                                                {{ $movement->item->satuan }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- User --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($movement->user)
                                            <div class="text-gray-800 text-sm">
                                                {{ $movement->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $movement->user->email }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                User tidak tercatat
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Keterangan --}}
                                    <td class="px-4 py-2 border-b align-top text-gray-700">
                                        @if ($movement->keterangan)
                                            {{ $movement->keterangan }}
                                        @else
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                        Belum ada transaksi stok yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
