<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Detail Permintaan ATK
                </h2>
                <p class="text-xs text-gray-400">{{ $atkShopRequest->request_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('atk-master.index') }}"
                    class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-slate-600 hover:bg-slate-700 text-white">
                    ← Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div
                    class="bg-emerald-900/30 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-lg text-sm">
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

            <!-- Request Information -->
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-100">Informasi Permintaan</h3>
                </div>

                <div class="px-4 py-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-slate-400">Nomor Permintaan</p>
                            <p class="text-sm text-slate-100 font-semibold">{{ $atkShopRequest->request_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Periode</p>
                            <p class="text-sm text-slate-100">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $atkShopRequest->period)->format('F Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Peminta</p>
                            <p class="text-sm text-slate-100">{{ $atkShopRequest->requestedBy->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Divisi</p>
                            <p class="text-sm text-slate-100">{{ $atkShopRequest->division->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Tanggal Ajukan</p>
                            <p class="text-sm text-slate-100">{{ $atkShopRequest->submitted_at->format('d F Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Status</p>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-900/30 text-blue-300 border border-blue-700/50">
                                {{ ucfirst($atkShopRequest->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-100">Daftar Item</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-800/50 border-b border-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Kode Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300">Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-300">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($atkShopRequest->items as $index => $requestItem)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-4 py-3 text-slate-300">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-slate-300 font-mono text-xs">
                                        {{ $requestItem->item->kode_barang }}</td>
                                    <td class="px-4 py-3 text-slate-100">{{ $requestItem->item->nama_barang }}</td>
                                    <td class="px-4 py-3 text-slate-300">{{ $requestItem->item->satuan }}</td>
                                    <td class="px-4 py-3 text-slate-100 text-right font-semibold">
                                        {{ $requestItem->qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-800/50 border-t border-slate-700">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-xs font-semibold text-slate-300">
                                    Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-slate-100">
                                    {{ $atkShopRequest->items->sum('qty') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            @if ($atkShopRequest->status !== 'done')
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                    <div class="px-4 py-4">
                        <div class="flex flex-col sm:flex-row gap-3 justify-end">

                            @if ($atkShopRequest->status === 'waiting_list')
                                <form method="POST"
                                    action="{{ route('atk-master.ready_to_pickup', $atkShopRequest) }}">
                                    @csrf
                                    <button class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded">
                                        Barang sudah datang & Siap diambil
                                    </button>
                                </form>
                            @endif

                            @if ($atkShopRequest->status === 'ready_to_pickup')
                                <form method="POST" action="{{ route('atk-master.finish', $atkShopRequest) }}">
                                    @csrf
                                    <button class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded">
                                        Selesaikan & Tambah ke Stok
                                    </button>
                                </form>
                            @endif

                            <!-- Approve Button -->
                            @if ($atkShopRequest->status === 'submitted')
                                <!-- Reject Button -->
                                <button type="button"
                                    onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                                    class="px-6 py-2.5 rounded-md text-sm font-semibold bg-red-500 hover:bg-red-600 text-white">
                                    ✗ Tolak Permintaan
                                </button>

                                {{-- Accept Button --}}
                                <form action="{{ route('atk-master.approve', $atkShopRequest) }}" method="POST"
                                    onsubmit="return confirm('Setujui permintaan ini? Status akan berubah menjadi waiting_list.')">
                                    @csrf
                                    <button type="submit"
                                        class="px-6 py-2.5 rounded-md text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                                        ✓ Setujui Permintaan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-slate-900 border border-slate-700 rounded-xl max-w-md w-full">
            <div class="px-4 py-3 border-b border-slate-800">
                <h3 class="text-lg font-semibold text-slate-100">Tolak Permintaan</h3>
            </div>

            <form action="{{ route('atk-master.reject', $atkShopRequest) }}" method="POST">
                @csrf
                <div class="px-4 py-4 space-y-4">
                    <p class="text-sm text-slate-300">
                        Permintaan akan dikembalikan ke peminta dengan status <strong>draft</strong>.
                        Peminta dapat memperbaiki dan mengajukan kembali.
                    </p>

                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-slate-300 mb-2">
                            Alasan Penolakan <span class="text-red-400">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required maxlength="1000"
                            class="w-full rounded-md border-slate-700 bg-slate-800 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Jelaskan alasan penolakan..."></textarea>
                        <p class="text-xs text-slate-400 mt-1">Maksimal 1000 karakter</p>
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-800 flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-md text-sm font-semibold bg-slate-600 hover:bg-slate-700 text-white">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-md text-sm font-semibold bg-red-500 hover:bg-red-600 text-white">
                        Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
