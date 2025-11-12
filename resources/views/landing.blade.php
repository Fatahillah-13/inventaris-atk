<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Inventaris ATK - Hwaseung Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center">

    <div class="max-w-md w-full px-6">
        {{-- Logo + Nama Aplikasi --}}
        <div class="flex flex-col items-center mb-8">
            {{-- Logo bulat sederhana, nanti bisa diganti gambar asli --}}
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-14 h-14 rounded-full mb-3 object-cover">
            <div class="text-center">
                <div class="text-sm text-slate-400 tracking-[0.25em] uppercase mb-1">
                    Sistem Internal
                </div>
                <h1 class="text-2xl font-semibold text-slate-50">
                    Sistem Inventaris HRD
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Hwaseung Indonesia
                </p>
            </div>
        </div>

        {{-- Tombol-tombol utama --}}
        <div class="space-y-3">
            {{-- Permintaan ATK - coming soon --}}
            <button type="button" disabled
                class="w-full inline-flex items-center justify-between px-4 py-3 rounded-md border border-slate-700 bg-slate-900/60 text-xs font-semibold uppercase tracking-widest text-slate-500 cursor-not-allowed">
                <span>Permintaan ATK</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-amber-300">
                    COMING SOON
                </span>
            </button>

            {{-- Peminjaman ATK --}}
            @if (Route::has('public.loans.create'))
                <a href="{{ route('public.loans.create') }}"
                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-md bg-emerald-500 text-xs font-semibold uppercase tracking-widest text-slate-950 hover:bg-emerald-400">
                    Peminjaman Barang
                </a>
            @else
                <button type="button" disabled
                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-md bg-slate-800 text-xs font-semibold uppercase tracking-widest text-slate-400 cursor-not-allowed">
                    Peminjaman ATK (route belum diset)
                </button>
            @endif

            {{-- Login --}}
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                    class="w-full inline-flex items-center justify-center px-4 py-3 rounded-md border border-slate-600 text-xs font-semibold uppercase tracking-widest text-slate-100 hover:border-slate-300">
                    Login Admin / Staff
                </a>
            @endif
        </div>

        {{-- Footer kecil --}}
        <div class="mt-8 text-[10px] text-slate-500 text-center">
            © {{ date('Y') }} Sistem Inventaris ATK • Internal use only
        </div>
    </div>

</body>

</html>
