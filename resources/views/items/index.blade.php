<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Master ATK
        </h2>
    </x-slot>

    <div class="py-4 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-between">
            <form method="GET" action="{{ route('items.index') }}">
                <input type="text" name="q" placeholder="Cari kode/nama barang" value="{{ request('q') }}"
                    class="border rounded px-2 py-1">
                <button class="px-3 py-1 bg-blue-600 text-white rounded">Cari</button>
            </form>

            <a href="{{ route('items.create') }}" class="px-3 py-1 bg-green-600 text-white rounded">
                + Tambah Barang
            </a>
        </div>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">Kode</th>
                    <th class="border px-2 py-1">Nama</th>
                    <th class="border px-2 py-1">Kategori</th>
                    <th class="border px-2 py-1">Satuan</th>
                    <th class="border px-2 py-1">Stok Terkini</th>
                    <th class="border px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="border px-2 py-1">{{ $item->kode_barang }}</td>
                        <td class="border px-2 py-1">{{ $item->nama_barang }}</td>
                        <td class="border px-2 py-1">{{ $item->item_category }}</td>
                        <td class="border px-2 py-1">{{ $item->satuan }}</td>
                        <td class="border px-2 py-1">{{ $item->stok_terkini }}</td>
                        <td class="border px-2 py-1">
                            <a href="{{ route('items.edit', $item) }}" class="text-blue-600">Edit</a>
                            |
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>
