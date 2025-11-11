{{-- resources/views/stock/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Stok Barang
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan sukses --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter sederhana (opsional bisa dikembangkan nanti) --}}
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-600">
                        Daftar histori barang masuk dan keluar yang tercatat di sistem.
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('stock.masuk.create') }}"
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700">
                        + Barang Masuk
                    </a>
                    <a href="{{ route('stock.keluar.create') }}"
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700">
                        + Barang Keluar
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border-b text-left">Tanggal</th>
                            <th class="px-3 py-2 border-b text-left">Jenis</th>
                            <th class="px-3 py-2 border-b text-left">Barang</th>
                            <th class="px-3 py-2 border-b text-right">Jumlah</th>
                            <th class="px-3 py-2 border-b text-left">User</th>
                            <th class="px-3 py-2 border-b text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                {{-- Tanggal --}}
                                <td class="px-3 py-2 border-b align-top">
                                    {{ \Carbon\Carbon::parse($movement->tanggal)->format('d-m-Y') }}
                                </td>

                                {{-- Jenis (Masuk / Keluar) --}}
                                <td class="px-3 py-2 border-b align-top">
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
                                <td class="px-3 py-2 border-b align-top">
                                    @if ($movement->item)
                                        <div class="font-semibold text-gray-800">
                                            {{ $movement->item->nama_barang }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Kode: {{ $movement->item->kode_barang }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Barang sudah dihapus</span>
                                    @endif
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-3 py-2 border-b align-top text-right">
                                    <span class="font-semibold">
                                        {{ $movement->jumlah }}
                                    </span>
                                    @if ($movement->item)
                                        <span class="text-xs text-gray-500">
                                            {{ $movement->item->satuan }}
                                        </span>
                                    @endif
                                </td>

                                {{-- User --}}
                                <td class="px-3 py-2 border-b align-top">
                                    @if ($movement->user)
                                        <div class="text-gray-800 text-sm">
                                            {{ $movement->user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $movement->user->email }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">User tidak ditemukan</span>
                                    @endif
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-3 py-2 border-b align-top">
                                    @if ($movement->keterangan)
                                        <span class="text-sm text-gray-700">
                                            {{ $movement->keterangan }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            -
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
                                    Belum ada transaksi stok yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
