<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Keranjang Permintaan
                </h2>
                <p class="text-xs text-gray-400">Periode: {{ now()->format('F Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('atk.catalog') }}"
                    class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-blue-500 hover:bg-blue-600 text-white">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

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

            @if (!$atkShopRequest || $atkShopRequest->items->isEmpty())
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-8 text-center">
                    <p class="text-slate-400 mb-4">Keranjang Anda kosong.</p>
                    <a href="{{ route('atk.catalog') }}"
                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                        Lihat Katalog
                    </a>
                </div>
            @else
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-800">
                        <h3 class="text-sm font-semibold text-slate-100">Item dalam Keranjang</h3>
                    </div>

                    <div class="divide-y divide-slate-800">
                        @foreach ($atkShopRequest->items as $requestItem)
                            <div class="px-4 py-4 flex items-center justify-between gap-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-slate-100 text-sm">{{ $requestItem->item->nama_barang }}</h4>
                                    <p class="text-xs text-slate-400">{{ $requestItem->item->kode_barang }}</p>
                                    <p class="text-xs text-slate-500 mt-1">Satuan: {{ $requestItem->item->satuan }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <form action="{{ route('atk.cart.update', $requestItem) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input 
                                            type="number" 
                                            name="qty" 
                                            value="{{ $requestItem->qty }}" 
                                            min="1"
                                            class="w-20 rounded-md border-slate-700 bg-slate-800 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                                        >
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-500 hover:bg-blue-600 text-white">
                                            Update
                                        </button>
                                    </form>

                                    <form action="{{ route('atk.cart.remove', $requestItem) }}" method="POST" 
                                        onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-md text-xs font-semibold bg-red-500 hover:bg-red-600 text-white">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-4 py-4 border-t border-slate-800 bg-slate-900/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-slate-400">Total Item:</span>
                            <span class="text-sm font-semibold text-slate-100">{{ $atkShopRequest->items->count() }} item</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-400">Total Jumlah:</span>
                            <span class="text-sm font-semibold text-slate-100">{{ $atkShopRequest->items->sum('qty') }}</span>
                        </div>
                    </div>

                    <div class="px-4 py-4 border-t border-slate-800 flex justify-end">
                        <form action="{{ route('atk.checkout') }}" method="POST"
                            onsubmit="return confirm('Ajukan permintaan ini? Setelah diajukan, keranjang tidak dapat diubah.')">
                            @csrf
                            <button type="submit"
                                class="px-6 py-2.5 rounded-md text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                                ✓ Ajukan Permintaan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
