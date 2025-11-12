{{-- resources/views/requests/public_create.blade.php --}}
<x-guest-layout>
    <div class="max-w-xl mx-auto mt-10 bg-white shadow-sm rounded-lg p-6">

        <h1 class="text-xl font-semibold text-gray-800 mb-1">
            Form Permintaan ATK
        </h1>
        <p class="text-xs text-gray-500 mb-4">
            Silakan isi data berikut untuk permintaan alat tulis kantor. Setelah mengirim form ini,
            harap datang ke bagian ATK untuk pengambilan barang.
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

        <form method="POST" action="{{ route('public.requests.store') }}" class="space-y-4">
            @csrf

            {{-- Nama Peminta --}}
            <div>
                <label for="peminta" class="block text-sm font-medium text-gray-700">
                    Nama Peminta
                </label>
                <input type="text" name="peminta" id="peminta" value="{{ old('peminta') }}"
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

            {{-- Barang yang diminta --}}
            <div>
                <label for="item_id" class="block text-sm font-medium text-gray-700">
                    Barang yang diminta
                </label>
                <select name="item_id" id="item_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->kode_barang }} - {{ $item->nama_barang }}
                            (stok: {{ $item->stok_terkini }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    Pastikan stok masih mencukupi. Sistem akan menolak jika stok tidak cukup.
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

            {{-- Tanggal --}}
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700">
                    Tanggal Permintaan
                </label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">
                    Keterangan (opsional)
                </label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: untuk meeting, training, project tertentu, dll.">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Kirim Permintaan ATK
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
