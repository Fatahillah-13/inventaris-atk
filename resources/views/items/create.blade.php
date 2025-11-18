{{-- resources/views/items/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Barang ATK
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
                <form method="POST" action="{{ route('items.store') }}">
                    @csrf

                    {{-- Kode Barang --}}
                    <div class="mb-4">
                        <label for="kode_barang" class="block text-sm font-medium text-gray-700">
                            Kode Barang
                        </label>
                        <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                        <p class="text-xs text-gray-400 mt-1">
                            Contoh: PULPEN-BIRU-001
                        </p>
                    </div>

                    {{-- Nama Barang --}}
                    <div class="mb-4">
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">
                            Nama Barang
                        </label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                        <p class="text-xs text-gray-400 mt-1">
                            Contoh: Pulpen Gel Biru 0.5mm
                        </p>
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label for="item_category" class="block text-sm font-medium text-gray-700">
                            Kategori
                        </label>
                        <input type="text" name="item_category" id="item_category" value="{{ old('item_category') }}"
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
                            <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>box</option>
                            <option value="rim" {{ old('satuan') == 'rim' ? 'selected' : '' }}>rim</option>
                            <option value="pak" {{ old('satuan') == 'pak' ? 'selected' : '' }}>pak</option>
                            <option value="lainnya" {{ old('satuan') == 'lainnya' ? 'selected' : '' }}>lainnya</option>
                        </select>
                    </div>

                    {{-- Stok Awal --}}
                    <div class="mb-4">
                        <label for="stok_awal" class="block text-sm font-medium text-gray-700">
                            Stok Awal
                        </label>
                        <input type="number" name="stok_awal" id="stok_awal" min="0"
                            value="{{ old('stok_awal', 0) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                        <p class="text-xs text-gray-400 mt-1">
                            Isi 0 jika belum ada stok fisik, nanti bisa ditambah lewat menu Barang Masuk.
                        </p>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-4">
                        <label for="catatan" class="block text-sm font-medium text-gray-700">
                            Catatan (opsional)
                        </label>
                        <textarea name="catatan" id="catatan" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Hanya digunakan untuk divisi tertentu, merk tertentu, dsb.">{{ old('catatan') }}</textarea>
                    </div>

                    {{-- Barang ini bisa dipinjam? --}}
                    <div class="flex items-center">
                        <input id="can_be_loaned" name="can_be_loaned" type="checkbox" value="1"
                            {{ old('can_be_loaned') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="can_be_loaned" class="ml-2 block text-sm text-gray-700">
                            Barang ini dapat dipinjam (misalnya: laptop, projector, drone, kabel HDMI, dll.)
                        </label>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('items.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
