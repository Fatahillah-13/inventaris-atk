<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Katalog ATK
                </h2>
                <p class="text-xs text-gray-400">Pilih barang yang ingin diminta</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('atk.cart') }}"
                    class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                    🛒 Lihat Keranjang
                </a>
                <a href="{{ route('atk.my-requests') }}"
                    class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-blue-500 hover:bg-blue-600 text-white">
                    📋 Permintaan Saya
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-emerald-900/30 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($items->isEmpty())
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-8 text-center">
                    <p class="text-slate-400">Tidak ada barang yang dapat diminta saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($items as $item)
                        <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 hover:border-emerald-400 transition">
                            <div class="mb-3">
                                <h3 class="font-semibold text-slate-100 text-sm mb-1">{{ $item->nama_barang }}</h3>
                                <p class="text-xs text-slate-400">{{ $item->kode_barang }}</p>
                                @if ($item->category)
                                    <p class="text-xs text-slate-500 mt-1">{{ $item->category->nama }}</p>
                                @endif
                            </div>

                            <div class="mb-3">
                                <p class="text-xs text-slate-400">Satuan: <span class="text-slate-200">{{ $item->satuan }}</span></p>
                            </div>

                            <form action="{{ route('atk.cart.add') }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                
                                <div>
                                    <label for="qty_{{ $item->id }}" class="block text-xs text-slate-400 mb-1">Jumlah</label>
                                    <input 
                                        type="number" 
                                        id="qty_{{ $item->id }}" 
                                        name="qty" 
                                        value="1" 
                                        min="1"
                                        class="w-full rounded-md border-slate-700 bg-slate-800 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                                        required
                                    >
                                </div>

                                <button type="submit"
                                    class="w-full px-3 py-2 rounded-md text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                                    + Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
