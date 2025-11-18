<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            Edit Kategori Barang
        </h2>
    </x-slot>

    <div class="py-6 bg-slate-950 min-h-screen">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-rose-500/10 border border-rose-500/40 text-rose-200 text-sm px-3 py-2 rounded">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('item-categories.update', $category) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-slate-100">
                            Kode Kategori
                        </label>
                        <input type="text" name="kode" value="{{ old('kode', $category->kode) }}" required
                            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-900 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-100">
                            Nama Kategori
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $category->nama) }}" required
                            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-900 text-sm text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 text-emerald-500 border-slate-600 bg-slate-900 rounded">
                        <label for="is_active" class="text-sm text-slate-200">
                            Kategori aktif
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-600">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('item-categories.index') }}"
                            class="ml-2 text-xs text-slate-400 hover:text-slate-200">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
