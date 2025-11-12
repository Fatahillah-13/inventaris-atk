<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Permintaan ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash message (kalau nanti diperlukan) --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Header card --}}
                <div
                    class="px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Daftar Permintaan ATK
                        </h3>
                        <p class="text-xs text-gray-500">
                            Data permintaan ATK yang masuk dari karyawan. Stok sudah otomatis berkurang saat permintaan
                            dicatat.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        {{-- Shortcut ke form publik (opsional) --}}
                        @if (Route::has('public.requests.create'))
                            <a href="{{ route('public.requests.create') }}" target="_blank"
                                class="inline-flex items-center px-3 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-900">
                                Buka Form Permintaan
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Pencarian --}}
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('requests.index') }}"
                        class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <label for="q" class="sr-only">Pencarian</label>
                            <input type="text" id="q" name="q" value="{{ $q }}"
                                placeholder="Cari peminta / departemen / barang / keterangan"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Cari
                        </button>
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
                                    Kode</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Peminta</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Barang</th>
                                <th
                                    class="px-4 py-2 border-b text-right text-xs font-semibold uppercase tracking-wider">
                                    Jumlah</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    User Internal</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($requests as $req)
                                <tr class="hover:bg-gray-50">
                                    {{-- Tanggal --}}
                                    <td class="px-4 py-2 border-b align-top text-gray-800 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($req->tanggal)->format('d-m-Y') }}
                                    </td>

                                    {{-- Kode --}}
                                    <td class="px-4 py-2 border-b align-top text-gray-800">
                                        <span class="font-semibold">{{ $req->kode_request }}</span>
                                    </td>

                                    {{-- Peminta --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        <div class="text-gray-900 font-semibold">
                                            {{ $req->peminta }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $req->departemen }}
                                        </div>
                                    </td>

                                    {{-- Barang --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($req->item)
                                            <div class="text-gray-900 font-semibold">
                                                {{ $req->item->nama_barang }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Kode: {{ $req->item->kode_barang }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                Barang tidak ditemukan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Jumlah --}}
                                    <td class="px-4 py-2 border-b align-top text-right">
                                        <span class="font-semibold text-gray-900">
                                            {{ $req->jumlah }}
                                        </span>
                                        @if ($req->item)
                                            <span class="text-xs text-gray-500">
                                                {{ $req->item->satuan }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- User internal --}}
                                    <td class="px-4 py-2 border-b align-top">
                                        @if ($req->user)
                                            <div class="text-gray-900 text-sm">
                                                {{ $req->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $req->user->email }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                (Public form)
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Keterangan --}}
                                    <td class="px-4 py-2 border-b align-top text-gray-700">
                                        @if ($req->keterangan)
                                            {{ $req->keterangan }}
                                        @else
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
                                        Belum ada permintaan ATK yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
