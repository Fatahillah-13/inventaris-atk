<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Divisi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-lg mx-auto bg-white shadow-sm sm:rounded-lg p-6">

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('divisions.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Nama Divisi</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium">Kode Divisi</label>
                    <input type="text" name="kode" value="{{ old('kode') }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="pt-3">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
