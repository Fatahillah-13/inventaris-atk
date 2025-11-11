{{-- resources/views/loans/public_create.blade.php --}}
<x-guest-layout>
    <div class="max-w-xl mx-auto mt-10 bg-white shadow-sm rounded-lg p-6">

        <h1 class="text-xl font-semibold text-gray-800 mb-1">
            Form Peminjaman ATK
        </h1>
        <p class="text-xs text-gray-500 mb-4">
            Silakan isi data berikut untuk peminjaman alat tulis. Setelah mengirim form ini, harap datang ke bagian ATK
            untuk pengambilan barang.
        </p>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('public.loans.store') }}" class="space-y-4">
            @csrf

            {{-- Nama Peminjam --}}
            <div>
                <label for="peminjam" class="block text-sm font-medium text-gray-700">
                    Nama Peminjam
                </label>
                <input type="text" name="peminjam" id="peminjam" value="{{ old('peminjam') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            {{-- Departemen / Divisi --}}
            <div>
                <label for="departemen" class="block text-sm font-medium text-gray-700">
                    Departemen / Divisi
                </label>
                <input type="text" name="departemen" id="departemen" value="{{ old('departemen') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            {{-- Barang yang dipinjam --}}
            <div>
                <label for="item_id" class="block text-sm font-medium text-gray-700">
                    Barang yang ingin dipinjam
                </label>
                <select name="item_id" id="item_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}
                            @if ($item->stok_terkini <= 0) disabled @endif>
                            {{ $item->kode_barang }} - {{ $item->nama_barang }}
                            (stok: {{ $item->stok_terkini }})
                            @if ($item->stok_terkini <= 0)
                                - HABIS
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    Barang dengan stok 0 tidak bisa dipinjam (ditandai HABIS).
                </p>
            </div>

            {{-- Jumlah --}}
            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-700">
                    Jumlah
                </label>
                <input type="number" name="jumlah" id="jumlah" min="1" value="{{ old('jumlah', 1) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            {{-- Tanggal Pinjam --}}
            <div>
                <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700">
                    Tanggal Pinjam
                </label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                    value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            {{-- Tanggal Rencana Kembali --}}
            <div>
                <label for="tanggal_rencana_kembali" class="block text-sm font-medium text-gray-700">
                    Tanggal Rencana Kembali (opsional)
                </label>
                <input type="date" name="tanggal_rencana_kembali" id="tanggal_rencana_kembali"
                    value="{{ old('tanggal_rencana_kembali') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">
                    Keterangan (opsional)
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: Untuk meeting, proyek tertentu, dll.">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Kirim Permintaan Peminjaman
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
