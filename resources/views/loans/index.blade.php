<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Peminjaman ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan sukses --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter status --}}
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-600">
                        Kelola peminjaman alat tulis: lihat mana yang masih dipinjam dan mana yang sudah dikembalikan.
                    </p>
                </div>

                <form method="GET" class="flex items-center gap-2 text-sm">
                    <label for="status" class="text-gray-700">Filter:</label>
                    <select name="status" id="status"
                        class="rounded border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="dipinjam" {{ ($status ?? '') === 'dipinjam' ? 'selected' : '' }}>Masih dipinjam
                        </option>
                        <option value="dikembalikan" {{ ($status ?? '') === 'dikembalikan' ? 'selected' : '' }}>Sudah
                            dikembalikan</option>
                    </select>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border-b text-left">Kode</th>
                            <th class="px-3 py-2 border-b text-left">Tanggal Pinjam</th>
                            <th class="px-3 py-2 border-b text-left">Peminjam</th>
                            <th class="px-3 py-2 border-b text-left">Barang</th>
                            <th class="px-3 py-2 border-b text-right">Jumlah</th>
                            <th class="px-3 py-2 border-b text-left">Status</th>
                            <th class="px-3 py-2 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr class="hover:bg-gray-50">
                                {{-- Kode --}}
                                <td class="px-3 py-2 border-b align-top">
                                    <a href="{{ route('loans.show', $loan) }}"
                                        class="text-indigo-600 hover:underline text-sm font-semibold">
                                        {{ $loan->kode_loan }}
                                    </a>
                                </td>

                                {{-- Tanggal Pinjam --}}
                                <td class="px-3 py-2 border-b align-top">
                                    {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d-m-Y') }}
                                    @if ($loan->tanggal_rencana_kembali)
                                        <div class="text-xs text-gray-500">
                                            Rencana kembali:
                                            {{ \Carbon\Carbon::parse($loan->tanggal_rencana_kembali)->format('d-m-Y') }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Peminjam --}}
                                <td class="px-3 py-2 border-b align-top">
                                    <div class="text-gray-800 font-semibold">
                                        {{ $loan->peminjam }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $loan->departemen }}
                                    </div>
                                </td>

                                {{-- Barang --}}
                                <td class="px-3 py-2 border-b align-top">
                                    @if ($loan->item)
                                        <div class="font-semibold text-gray-800">
                                            {{ $loan->item->nama_barang }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Kode: {{ $loan->item->kode_barang }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Barang sudah dihapus</span>
                                    @endif
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-3 py-2 border-b align-top text-right">
                                    <span class="font-semibold">{{ $loan->jumlah }}</span>
                                    @if ($loan->item)
                                        <span class="text-xs text-gray-500">{{ $loan->item->satuan }}</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-2 border-b align-top">
                                    @if ($loan->status === 'dipinjam')
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Masih dipinjam
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Dikembalikan
                                        </span>
                                        @if ($loan->tanggal_kembali)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d-m-Y') }}
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-3 py-2 border-b align-top text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <a href="{{ route('loans.show', $loan) }}"
                                            class="text-xs text-indigo-600 hover:underline">
                                            Detail
                                        </a>

                                        @if ($loan->status === 'dipinjam')
                                            <form action="{{ route('loans.return', $loan) }}" method="POST"
                                                onsubmit="return confirm('Tandai peminjaman ini sudah dikembalikan?');">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                                    Tandai Dikembalikan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
