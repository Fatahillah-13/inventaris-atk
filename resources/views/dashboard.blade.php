<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Dashboard SEDIA
                </h2>
                <p class="text-xs text-gray-400">
                    Ringkasan inventaris & aktivitas terbaru
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Ringkasan angka --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Barang</p>
                    <p class="text-2xl font-semibold text-slate-50">{{ $totalItems ?? '-' }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Peminjaman Aktif</p>
                    <p class="text-2xl font-semibold text-emerald-400">{{ $activeLoans ?? '-' }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Permintaan Hari Ini</p>
                    <p class="text-2xl font-semibold text-sky-400">{{ $todayRequests ?? '-' }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Stok Menipis</p>
                    <p class="text-2xl font-semibold text-rose-400">{{ $lowStocks ?? '-' }}</p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Master Barang --}}
                <a href="{{ route('items.index') }}"
                    class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex items-center gap-3 hover:border-emerald-400 hover:bg-slate-900/90 transition">
                    <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                        <span class="text-sm">📦</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">Master Barang</p>
                        <p class="text-xs text-slate-400">Kelola data barang & stok</p>
                    </div>
                </a>

                {{-- Riwayat Stok --}}
                <a href="{{ route('stock.index') }}"
                    class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex items-center gap-3 hover:border-emerald-400 hover:bg-slate-900/90 transition">
                    <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                        <span class="text-sm">📊</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">Riwayat Stok</p>
                        <p class="text-xs text-slate-400">Barang masuk & keluar</p>
                    </div>
                </a>

                {{-- Peminjaman --}}
                <a href="{{ route('loans.index') }}"
                    class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex items-center gap-3 hover:border-emerald-400 hover:bg-slate-900/90 transition">
                    <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                        <span class="text-sm">🔁</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">Peminjaman</p>
                        <p class="text-xs text-slate-400">Pantau status peminjaman</p>
                    </div>
                </a>

                {{-- Permintaan ATK --}}
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff_pengelola')
                    <a href="{{ route('atk.catalog') }}"
                        class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex items-center gap-3 hover:border-emerald-400 hover:bg-slate-900/90 transition">
                        <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                            <span class="text-sm">📝</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-100">Permintaan ATK</p>
                            <p class="text-xs text-slate-400">Request ATK Bulanan</p>
                        </div>
                    </a>
                @endif

                @if (auth()->user()->role === 'atk_master')
                    <a href="{{ route('atk-master.index') }}"
                        class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex items-center gap-3 hover:border-emerald-400 hover:bg-slate-900/90 transition">
                        <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center">
                            <span class="text-sm">📝</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-100">Permintaan ATK</p>
                            <p class="text-xs text-slate-400">Daftar permintaan dari karyawan</p>
                        </div>
                    </a>
                @endif
            </div>

            {{-- Aktivitas terbaru (contoh stok movements) --}}
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-100">Aktivitas Terbaru</h3>
                    <span class="text-[11px] text-slate-500">
                        Menampilkan 3 aktivitas terakhir
                    </span>
                </div>
                <div class="divide-y divide-slate-800">
                    @forelse($recentMovements ?? [] as $m)
                        <div class="px-5 py-3 flex items-start justify-between text-sm">
                            <div>
                                <p class="font-medium text-slate-100">
                                    {{ ucfirst($m->jenis) }} – {{ $m->item->nama_barang ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    Divisi: {{ $m->division->nama ?? '-' }} ·
                                    {{ $m->tanggal }} ·
                                    oleh {{ $m->user->name ?? 'Sistem' }}
                                </p>
                                @if ($m->keterangan)
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        {{ $m->keterangan }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-slate-50">{{ $m->jumlah }}</p>
                                <p class="text-[11px] text-slate-500">{{ $m->item->satuan ?? '' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-4 text-sm text-slate-400">
                            Belum ada aktivitas terbaru.
                        </div>
                    @endforelse
                </div>

                {{-- Tombol lihat semua --}}
                <div class="px-5 py-3 border-t border-slate-800 flex justify-end">
                    <a href="{{ route('stock.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-300 hover:text-emerald-200">
                        Lihat semua aktivitas stok
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
