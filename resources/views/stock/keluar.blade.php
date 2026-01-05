{{-- resources/views/stock/keluar.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Barang Keluar
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Pesan sukses / error --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

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
                <form method="POST" action="{{ route('stock.keluar.store') }}">
                    @csrf

                    {{-- Pilih Divisi --}}
                    <div class="mb-4">
                        <label for="division_id" class="block text-sm font-medium text-gray-700">
                            Divisi
                        </label>
                        <select name="division_id" id="division_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Barang --}}
                    <div class="mb-4">
                        <label for="item_id" class="block text-sm font-medium text-gray-700">
                            Barang
                        </label>
                        <select name="item_id" id="item_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                            <option value="">-- Pilih divisi terlebih dahulu --</option>
                        </select>
                    </div>

                    {{-- Jumlah --}}
                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700">
                            Jumlah
                        </label>
                        <input type="number" name="jumlah" id="jumlah" min="1" value="{{ old('jumlah') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-4">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">
                            Tanggal
                        </label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Keterangan (opsional)
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Pemakaian Divisi HR, proyek A">
                            {{ old('keterangan') }}
                        </textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('stock.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Kembali
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Simpan Barang Keluar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const divisionSelect = document.getElementById('division_id');
            const itemSelect = document.getElementById('item_id');

            function resetItems(message) {
                itemSelect.innerHTML = `<option value="">${message}</option>`;
                itemSelect.disabled = true;
            }

            async function loadItemsByDivision(divisionId) {
                resetItems('Memuat barang...');

                try {
                    const res = await fetch(`/ajax/items-by-division/${divisionId}`);
                    const data = await res.json();

                    itemSelect.innerHTML = '';
                    itemSelect.disabled = false;

                    if (!Array.isArray(data) || data.length === 0) {
                        resetItems('Tidak ada barang di divisi ini');
                        return;
                    }

                    // placeholder
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = '-- Pilih Barang --';
                    itemSelect.appendChild(placeholder);

                    // render options
                    let selectableCount = 0;
                    let lastSelectableId = null;

                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;

                        const stok = Number(item.stok || 0);
                        opt.textContent = `${item.nama_barang} - stok: ${stok}`;

                        // kalau stok 0 -> disabled tapi tetap tampil
                        if (stok <= 0) {
                            opt.disabled = true;
                        } else {
                            selectableCount++;
                            lastSelectableId = String(item.id);
                        }

                        itemSelect.appendChild(opt);
                    });

                    // auto-select kalau hanya 1 yang selectable
                    if (selectableCount === 1 && lastSelectableId) {
                        itemSelect.value = lastSelectableId;
                    }
                } catch (e) {
                    console.error(e);
                    resetItems('Gagal memuat barang');
                }
            }

            divisionSelect.addEventListener('change', function() {
                const divisionId = this.value;
                if (!divisionId) {
                    resetItems('-- Pilih divisi terlebih dahulu --');
                    return;
                }

                loadItemsByDivision(divisionId);
            });

            // jika ada old division (mis. setelah error submit), auto load items
            if (divisionSelect.value) {
                loadItemsByDivision(divisionSelect.value);
            } else {
                resetItems('-- Pilih divisi terlebih dahulu --');
            }
        });
    </script>
</x-app-layout>
