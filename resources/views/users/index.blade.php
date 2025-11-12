<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Kelola akun pengguna sistem inventaris ATK.
                </p>

                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                    + Tambah User
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border-b text-left">Nama</th>
                            <th class="px-3 py-2 border-b text-left">Email</th>
                            <th class="px-3 py-2 border-b text-left">Role</th>
                            <th class="px-3 py-2 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border-b align-top">
                                    <div class="font-semibold text-gray-800">
                                        {{ $user->name }}
                                    </div>
                                    @if (auth()->id() === $user->id)
                                        <div class="text-xs text-green-600">(Anda)</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 border-b align-top">
                                    {{ $user->email }}
                                </td>
                                <td class="px-3 py-2 border-b align-top">
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 border-b align-top text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="text-xs text-indigo-600 hover:underline">
                                            Edit
                                        </a>

                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">
                                    Belum ada user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
