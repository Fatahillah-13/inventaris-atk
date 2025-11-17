<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Divisi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-4 flex justify-end">
                <a href="{{ route('divisions.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs uppercase font-semibold">
                    Tambah Divisi
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2 border-b text-left">Nama Divisi</th>
                            <th class="px-4 py-2 border-b text-left">Kode</th>
                            <th class="px-4 py-2 border-b text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($divisions as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border-b">{{ $d->nama }}</td>
                                <td class="px-4 py-2 border-b">{{ $d->kode }}</td>
                                <td class="px-4 py-2 border-b text-right">
                                    <a href="{{ route('divisions.edit', $d) }}"
                                        class="text-blue-600 hover:underline mr-3 text-sm">Edit</a>

                                    <form action="{{ route('divisions.destroy', $d) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus divisi ini?')"
                                            class="text-red-600 hover:underline text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $divisions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
