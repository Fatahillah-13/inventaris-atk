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

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    NIK (Nomor Induk Karyawan)
                </label>
                <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: 2412074308">
                <p id="employee_help" class="text-xs text-gray-400 mt-1">
                    Isi NIK, nama dan departemen akan terisi otomatis.
                </p>
                <p id="employee_error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            {{-- Nama Peminjam auto-fill --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Nama Peminjam
                </label>
                <input type="text" name="peminjam" id="employee_name" value="{{ old('peminjam') }}" readonly
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Departemen auto-fill --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Departemen / Divisi Peminjam
                </label>
                <input type="text" name="departemen" id="employee_department" value="{{ old('departemen') }}"
                    readonly
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
                <button type="submit" id="submit_btn"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Kirim Peminjaman
                </button>
            </div>
        </form>
    </div>

    <script>
        const nikInput = document.getElementById('employee_id');
        const nameInput = document.getElementById('employee_name');
        const deptInput = document.getElementById('employee_department');
        const errorEl = document.getElementById('employee_error');
        const submitBtn = document.getElementById('submit_btn');

        let nikTimer = null;
        let nikValid = false;

        function setNikState(valid, message = '') {
            nikValid = valid;
            if (valid) {
                errorEl.classList.add('hidden');
                errorEl.textContent = '';
            } else {
                if (message) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                }
            }
            submitBtn.disabled = !nikValid;
            submitBtn.classList.toggle('opacity-50', !nikValid);
            submitBtn.classList.toggle('cursor-not-allowed', !nikValid);
        }

        async function lookupNik(nik) {
            if (!nik) {
                nameInput.value = '';
                deptInput.value = '';
                setNikState(false, 'NIK wajib diisi.');
                return;
            }

            setNikState(false, 'Memeriksa NIK...');
            try {
                const res = await fetch(`/ajax/employee-by-nik/${encodeURIComponent(nik)}`);
                const data = await res.json();

                if (!res.ok || !data.found) {
                    nameInput.value = '';
                    deptInput.value = '';
                    setNikState(false, data.message || 'NIK tidak ditemukan.');
                    return;
                }

                nameInput.value = data.name || '';
                deptInput.value = data.department || '';
                setNikState(true);
            } catch (e) {
                nameInput.value = '';
                deptInput.value = '';
                setNikState(false, 'Gagal mengambil data karyawan. Coba lagi.');
            }
        }

        nikInput.addEventListener('input', function() {
            const nik = this.value.trim();

            // reset autofill while typing
            nameInput.value = '';
            deptInput.value = '';
            setNikState(false);

            clearTimeout(nikTimer);
            nikTimer = setTimeout(() => lookupNik(nik), 450);
        });

        // saat load halaman, jika ada old('employee_id') maka auto lookup
        if (nikInput.value && nikInput.value.trim() !== '') {
            lookupNik(nikInput.value.trim());
        } else {
            // default: jangan bisa submit sebelum NIK valid
            setNikState(false);
        }

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
