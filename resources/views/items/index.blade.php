{{-- resources/views/items/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            Master ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-4 lg:px-6 ">

            {{-- Pesan sukses / error --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm py-4 sm:rounded-lg overflow-hidden">
                {{-- Header card: judul + tombol tambah --}}
                <div
                    class="px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Daftar Master ATK
                        </h3>
                        <p class="text-xs text-gray-500">
                            Kelola data barang ATK: kode, nama, kategori, satuan, dan stok terkini.
                        </p>
                    </div>

                    <a href="{{ route('items.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                        + Tambah Barang
                    </a>
                </div>

                {{-- Area pencarian --}}
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('items.index') }}"
                        class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <label class="sr-only" for="q">Pencarian</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}"
                            placeholder="Cari kode / nama barang"
                            class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- Tabel data --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Kode</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Nama</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Kategori</th>
                                <th class="px-4 py-2 border-b text-left text-xs font-semibold uppercase tracking-wider">
                                    Satuan</th>
                                <th
                                    class="px-4 py-2 border-b text-right text-xs font-semibold uppercase tracking-wider">
                                    Stok Terkini</th>
                                <th
                                    class="px-4 py-2 border-b text-center text-xs font-semibold uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse ($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b align-top text-gray-800">
                                        <span class="font-semibold">{{ $item->kode_barang }}</span>
                                    </td>
                                    <td class="px-4 py-2 border-b align-top text-gray-800">
                                        {{ $item->nama_barang }}
                                    </td>
                                    <td class="px-4 py-2 border-b align-top text-gray-700">
                                        {{ $item->item_category ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border-b align-top text-gray-700">
                                        {{ $item->satuan }}
                                    </td>
                                    <td class="px-4 py-2 border-b align-top text-right">
                                        <span class="font-semibold text-gray-900">
                                            {{ $item->stok_terkini }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border-b align-top text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('items.edit', $item) }}"
                                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                                Edit
                                            </a>

                                            <form action="{{ route('items.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                        Belum ada data barang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Pagination (kalau pakai paginate di controller) --}}
            @if (method_exists($items, 'links'))
                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
