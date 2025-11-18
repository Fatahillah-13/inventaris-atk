{{-- resources/views/loans/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Peminjaman ATK
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Flash message --}}
            @if (session('success'))
                <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Kartu utama --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-[0.2em]">
                            Peminjaman Barang
                        </div>
                        <div class="text-lg font-semibold text-gray-800">
                            {{ $loan->kode_loan }}
                        </div>
                    </div>

                    <div class="text-right">
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
                        @endif
                    </div>
                </div>

                <div class="px-5 py-4 space-y-4 text-sm text-gray-700">

                    {{-- Info peminjam --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Peminjam
                        </h3>
                        <div class="text-gray-900 font-semibold">
                            {{ $loan->peminjam }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $loan->departemen }}
                        </div>
                    </div>

                    {{-- Info barang --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Barang
                        </h3>
                        @if ($loan->item)
                            <div class="text-gray-900 font-semibold">
                                {{ $loan->item->nama_barang }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Kode: {{ $loan->item->kode_barang }} • Satuan: {{ $loan->item->satuan }}
                            </div>
                        @else
                            <div class="text-xs text-gray-400 italic">
                                Data barang tidak ditemukan (mungkin sudah dihapus).
                            </div>
                        @endif
                    </div>

                    {{-- Divisi sumber stok --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Divisi Sumber Stok
                        </h3>
                        @if ($loan->division)
                            <div
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $loan->division->nama }}
                                @if ($loan->division->kode)
                                    <span class="ml-1 text-slate-500">({{ $loan->division->kode }})</span>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">
                                Divisi sumber stok belum tercatat.
                            </span>
                        @endif
                    </div>

                    {{-- Jumlah & tanggal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Jumlah
                            </h3>
                            <div class="text-gray-900 font-semibold">
                                {{ $loan->jumlah }}
                                @if ($loan->item)
                                    <span class="text-xs text-gray-500">{{ $loan->item->satuan }}</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Tanggal Pinjam
                            </h3>
                            <div class="text-gray-900">
                                {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d-m-Y') }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Rencana Kembali
                            </h3>
                            <div class="text-gray-900">
                                @if ($loan->tanggal_rencana_kembali)
                                    {{ \Carbon\Carbon::parse($loan->tanggal_rencana_kembali)->format('d-m-Y') }}
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ditentukan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Status pengembalian --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Status
                            </h3>
                            <div class="text-gray-900">
                                @if ($loan->status === 'dipinjam')
                                    Masih dipinjam
                                @else
                                    Sudah dikembalikan
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Tanggal Kembali
                            </h3>
                            <div class="text-gray-900">
                                @if ($loan->tanggal_kembali)
                                    {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d-m-Y') }}
                                @else
                                    <span class="text-xs text-gray-400 italic">
                                        Belum dikembalikan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- User internal yang mencatat (opsional) --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Dicatat oleh
                        </h3>
                        @if ($loan->user)
                            <div class="text-gray-900 text-sm">
                                {{ $loan->user->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $loan->user->email }}
                            </div>
                        @else
                            <div class="text-xs text-gray-400 italic">
                                User internal tidak tercatat.
                            </div>
                        @endif
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Keterangan
                        </h3>
                        <div class="text-gray-900">
                            @if ($loan->keterangan)
                                {{ $loan->keterangan }}
                            @else
                                <span class="text-xs text-gray-400 italic">Tidak ada keterangan.</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stok per divisi (saat ini) --}}
                @if ($loan->item && $loan->item->divisionStocks->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Stok per Divisi (saat ini)
                        </h3>

                        @php
                            $totalStok = $loan->item->divisionStocks->sum('stok_terkini');
                        @endphp

                        <div class="mb-1 text-xs text-gray-600">
                            Total stok: <span class="font-semibold text-gray-900">{{ $totalStok }}</span>
                            <span class="text-gray-400">
                                @if ($loan->item->satuan)
                                    ({{ $loan->item->satuan }})
                                @endif
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-1">
                            @foreach ($loan->item->divisionStocks as $ds)
                                @if ($ds->division)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ds->division->nama }}:
                                        <span class="ml-1 font-semibold">{{ $ds->stok_terkini }}</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Footer aksi --}}
                <div
                    class="px-5 py-3 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <a href="{{ route('loans.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                        Kembali ke daftar
                    </a>

                    @if ($loan->status === 'dipinjam')
                        <form action="{{ route('loans.return', $loan) }}" method="POST"
                            onsubmit="return confirm('Tandai peminjaman ini sudah dikembalikan?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-700">
                                Tandai Sudah Dikembalikan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
