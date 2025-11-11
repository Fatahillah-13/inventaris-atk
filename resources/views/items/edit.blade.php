{{-- resources/views/items/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Barang ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan error validasi --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <div class="font-semibold mb-2">Terjadi kesalahan:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('items.update', $item) }}">
                    @csrf
                    @method('PUT')

                    {{-- Kode Barang --}}
                    <div class="mb-4">
                        <label for="kode_barang" class="block text-sm font-medium text-gray-700">
                            Kode Barang
                        </label>
                        <input type="text" name="kode_barang" id="kode_barang"
                            value="{{ old('kode_barang', $item->kode_barang) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                        <p class="text-xs text-gray-400 mt-1">
                            Pastikan kode unik dan konsisten dengan sistem gudang.
                        </p>
                    </div>

                    {{-- Nama Barang --}}
                    <div class="mb-4">
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">
                            Nama Barang
                        </label>
                        <input type="text" name="nama_barang" id="nama_barang"
                            value="{{ old('nama_barang', $item->nama_barang) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label for="item_category" class="block text-sm font-medium text-gray-700">
                            Kategori
                        </label>
                        <input type="text" name="item_category" id="item_category"
                            value="{{ old('item_category', $item->item_category) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Pulpen, Kertas, Map">
                    </div>

                    {{-- Satuan --}}
                    <div class="mb-4">
                        <label for="satuan" class="block text-sm font-medium text-gray-700">
                            Satuan
                        </label>
                        <select name="satuan" id="satuan"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                            <option value="">-- Pilih Satuan --</option>
                            @php
                                $satuan = old('satuan', $item->satuan);
                            @endphp
                            <option value="pcs" {{ $satuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="box" {{ $satuan == 'box' ? 'selected' : '' }}>box</option>
                            <option value="rim" {{ $satuan == 'rim' ? 'selected' : '' }}>rim</option>
                            <option value="pak" {{ $satuan == 'pak' ? 'selected' : '' }}>pak</option>
                            <option value="lainnya" {{ $satuan == 'lainnya' ? 'selected' : '' }}>lainnya</option>
                        </select>
                    </div>

                    {{-- Info stok (hanya tampil, tidak bisa diubah) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Informasi Stok (tidak bisa diubah di sini)
                        </label>
                        <div class="mt-1 text-sm text-gray-700">
                            <div>Stok awal: <span class="font-semibold">{{ $item->stok_awal }}</span>
                                {{ $item->satuan }}</div>
                            <div>Stok terkini: <span class="font-semibold">{{ $item->stok_terkini }}</span>
                                {{ $item->satuan }}</div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Perubahan stok dilakukan melalui menu <strong>Barang Masuk</strong> dan <strong>Barang
                                Keluar</strong>.
                        </p>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-4">
                        <label for="catatan" class="block text-sm font-medium text-gray-700">
                            Catatan (opsional)
                        </label>
                        <textarea name="catatan" id="catatan" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Hanya digunakan untuk divisi tertentu, merk tertentu, dsb.">{{ old('catatan', $item->catatan) }}</textarea>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('items.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Kembali
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
