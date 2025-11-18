<x-guest-layout>
    <div class="max-w-xl mx-auto mt-10 bg-white shadow-sm rounded-lg p-6">

        <h1 class="text-xl font-semibold text-gray-800 mb-1">
            Form Peminjaman Barang
        </h1>
        <p class="text-xs text-gray-500 mb-4">
            Silakan isi data berikut untuk peminjaman barang. Stok akan dikurangi dari divisi yang dipilih.
        </p>

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

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
                <label class="block text-sm font-medium text-gray-700">
                    Nama Peminjam
                </label>
                <input type="text" name="peminjam" value="{{ old('peminjam') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Departemen / Divisi Peminjam (organisasional) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Departemen / Divisi Peminjam
                </label>
                <input type="text" name="departemen" value="{{ old('departemen') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Barang --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Barang yang dipinjam
                </label>
                <select name="item_id" id="item_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->kode_barang }} - {{ $item->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Divisi Sumber Stok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Divisi Sumber Stok
                </label>
                <select name="division_id" id="division_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">-- Pilih Divisi Pemilik Stok --</option>
                    {{-- @foreach ($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->nama }} @if ($div->kode)
                                ({{ $div->kode }})
                            @endif
                        </option>
                    @endforeach --}}
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    Stok akan dikurangi dari divisi yang dipilih di sini.
                </p>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Jumlah
                </label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Tanggal Pinjam --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Tanggal Pinjam
                </label>
                <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Tanggal Rencana Kembali --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Tanggal Rencana Kembali (opsional)
                </label>
                <input type="date" name="tanggal_rencana_kembali" value="{{ old('tanggal_rencana_kembali') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Keterangan (opsional)
                </label>
                <textarea name="keterangan" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: dipakai untuk training, presentasi, kegiatan tertentu, dll.">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Kirim Peminjaman
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const divisionSelect = document.getElementById('division_id');

            itemSelect.addEventListener('change', function() {
                const itemId = this.value;

                // reset division select
                divisionSelect.innerHTML = '<option value="">Memuat divisi...</option>';

                if (itemId === '') {
                    divisionSelect.innerHTML =
                        '<option value="">-- Pilih Barang terlebih dahulu --</option>';
                    return;
                }

                // Fetch divisi berdasarkan barang
                fetch(`/ajax/divisions-by-item/${itemId}`)
                    .then(response => response.json())
                    .then(data => {
                        divisionSelect.innerHTML = '';

                        if (data.length === 0) {
                            divisionSelect.innerHTML =
                                '<option value="">Tidak ada stok di divisi manapun</option>';
                            return;
                        }

                        // isi divisi yang punya stok
                        data.forEach(d => {
                            let label = `${d.nama}`;
                            if (d.kode) label += ` (${d.kode})`;
                            label += ` - Stok: ${d.stok}`;

                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = label;

                            divisionSelect.appendChild(opt);
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        divisionSelect.innerHTML =
                            '<option value="">Gagal memuat divisi</option>';
                    });
            });
        });
    </script>

</x-guest-layout>
