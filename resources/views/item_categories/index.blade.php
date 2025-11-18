<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    Kategori Barang
                </h2>
                <p class="text-xs text-gray-400">
                    Master kategori untuk pengelompokan barang (ATK, TECH, TOOLS, dll.)
                </p>
            </div>

            <a href="{{ route('item-categories.create') }}"
                class="inline-flex items-center px-3 py-2 rounded-md text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white">
                + Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/50 text-emerald-200 text-sm px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-slate-900 border border-slate-700/80 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-400">
                        Total: {{ $categories->total() }} kategori
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="bg-slate-900/80 text-slate-300 border-b border-slate-800 text-xs uppercase tracking-wide">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-center">Status</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($categories as $cat)
                                <tr class="hover:bg-slate-800/70">
                                    <td class="px-3 py-2 text-slate-100 font-semibold">
                                        {{ $cat->kode }}
                                    </td>
                                    <td class="px-3 py-2 text-slate-50">
                                        {{ $cat->nama }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        @if ($cat->is_active)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[11px] bg-emerald-500/15 text-emerald-300 border border-emerald-500/40">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[11px] bg-slate-800 text-slate-400 border border-slate-700">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="inline-flex gap-3 text-xs">
                                            <a href="{{ route('item-categories.edit', $cat) }}"
                                                class="text-emerald-300 hover:text-emerald-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('item-categories.destroy', $cat) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-sm text-slate-400">
                                        Belum ada kategori barang. Tambahkan minimal ATK, TECH, TOOLS, dll.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-800">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
