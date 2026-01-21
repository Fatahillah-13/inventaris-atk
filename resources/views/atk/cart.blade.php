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

            @if ($atkShopRequest && $atkShopRequest->rejection_reason)
                <div class="bg-amber-900/30 border border-amber-500/50 text-amber-200 px-4 py-3 rounded-lg text-sm">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold mb-1">Permintaan Anda Ditolak</p>
                            <p class="text-xs mb-2">
                                Ditolak oleh: <strong>{{ $atkShopRequest->rejectedBy->name ?? 'ATK Master' }}</strong> 
                                pada {{ $atkShopRequest->rejected_at?->format('d M Y H:i') }}
                            </p>
                            <p class="text-sm">
                                <strong>Alasan:</strong> {{ $atkShopRequest->rejection_reason }}
                            </p>
                            <p class="text-xs mt-2 italic">
                                Silakan perbaiki permintaan Anda dan ajukan kembali.
                            </p>
                        </div>
                    </div>
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
