{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Dashboard Inventaris HRD
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Sapaan singkat --}}
            <div class="mb-6">
                <p class="text-white text-sm">
                    Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                </p>
                <p class="text-gray-300 text-xs">
                    Pilih menu di bawah untuk mengelola inventaris HRD.
                </p>
            </div>

            {{-- Grid menu utama --}}
            @php
                $role = auth()->user()->role;
            @endphp
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card: Master Barang --}}
                <a href="{{ route('items.index') }}"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            Master Barang
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Lihat dan kelola data barang HRD (kode, nama, kategori, stok).
                        </p>
                        <span class="inline-flex items-center text-xs font-semibold text-indigo-600">
                            Buka Halaman
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                {{-- Card: Stok Masuk --}}
                <a href="{{ route('stock.masuk.create') }}"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            Barang Masuk
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Catat penambahan stok barang yang baru datang ke gudang.
                        </p>
                        <span class="inline-flex items-center text-xs font-semibold text-green-600">
                            Input Barang Masuk
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                {{-- Card: Stok Keluar --}}
                <a href="{{ route('stock.keluar.create') }}"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            Barang Keluar
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Catat barang ATK yang keluar untuk dipakai karyawan/divisi.
                        </p>
                        <span class="inline-flex items-center text-xs font-semibold text-red-600">
                            Input Barang Keluar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                {{-- Card: Riwayat Stok --}}
                <a href="{{ route('stock.index') }}"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            Riwayat Stok
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Lihat histori barang masuk dan keluar untuk semua item.
                        </p>
                        <span class="inline-flex items-center text-xs font-semibold text-gray-700">
                            Lihat Riwayat
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                {{-- (Opsional) Card lain untuk nanti: Permintaan ATK, Laporan, dll. --}}
                <a href="#"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 opacity-50 cursor-not-allowed">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            (Coming Soon) Permintaan ATK
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Fitur permintaan ATK oleh karyawan dengan approval.
                        </p>
                    </div>
                </a>

                {{-- Card: Peminjaman ATK (internal list) --}}
                <a href="{{ route('loans.index') }}"
                    class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            Peminjaman ATK
                        </h3>
                        <p class="text-sm text-gray-500 mb-3">
                            Lihat dan kelola daftar peminjaman barang HRD.
                        </p>
                        <span class="inline-flex items-center text-xs font-semibold text-gray-700">
                            Buka Daftar Peminjaman
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                @if ($role === 'admin')
                    {{-- Card Manajemen User --}}
                    <a href="{{ route('users.index') }}"
                        class="block bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition">
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                Manajemen User
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                Tambah, edit, dan hapus akun pengguna sistem inventaris.
                            </p>
                            <span class="inline-flex items-center text-xs font-semibold text-gray-700">
                                Buka Manajemen User
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </a>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
