<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>SEDIA - HRD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fade-in-up {
            opacity: 0;
            animation: fadeInUp .7s ease-out .1s forwards;
        }

        .fade-in-up-delayed {
            opacity: 0;
            animation: fadeInUp .7s ease-out .3s forwards;
        }

        .fade-in-delayed {
            opacity: 0;
            animation: fadeIn .8s ease-out .5s forwards;
        }
    </style>
</head>

<body>
    <div class="min-h-screen bg-slate-950 text-slate-50 relative overflow-hidden">

        {{-- Background image + overlay --}}
        <div class="absolute inset-0">
            <div class="w-full h-full bg-cover bg-center"
                style="background-image: url('{{ asset('images/bg-inventory.jpg') }}');">
            </div>
            <div class="absolute inset-0 bg-slate-950/80"></div>
        </div>

        {{-- Konten utama --}}
        <div class="relative z-10 flex flex-col min-h-screen">

            {{-- Header --}}
            <header class="px-6 sm:px-10 py-4 flex items-center justify-between fade-in-up">
                <div class="flex items-center gap-3">
                    {{-- Logo bulat --}}
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow-lg">
                    <div class="leading-tight">
                        <div class="text-xs font-semibold tracking-[0.25em] text-emerald-300">
                            SEDIA
                        </div>
                        <div class="text-[11px] text-slate-400">
                            HRD | Hwaseung Indonesia
                        </div>
                    </div>
                </div>

                {{-- Tombol Login kanan atas --}}
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-slate-900/80 border border-slate-700 text-xs sm:text-sm font-semibold text-slate-100 hover:bg-slate-800 hover:border-emerald-400 transition">
                    {{-- Icon login --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                    </svg>
                    <span>Login</span>
                </a>
            </header>

            {{-- Hero + Card --}}
            <main class="flex-1 flex items-center justify-center px-4 sm:px-6 pb-10">
                <div class="max-w-5xl w-full">

                    {{-- Judul --}}
                    <div class="text-center mb-8 sm:mb-10 fade-in-up-delayed">
                        <p class="text-xs sm:text-sm tracking-[0.35em] text-emerald-300 mb-2">
                            S . E . D . I . A
                        </p>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-slate-50 mb-3">
                            Sistem Elektronik Data Inventaris & Aset
                        </h1>
                        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto">
                            Pencatatan permintaan ATK, peminjaman barang, dan pergerakan stok
                            untuk kebutuhan kantor – cepat, tertib, dan terdokumentasi.
                        </p>
                    </div>

                    {{-- Card aksi (horizontal) --}}
                    <div class="flex flex-col md:flex-row gap-5 md:gap-6 justify-center items-stretch fade-in-delayed">
                        {{-- Card Peminjaman Barang --}}
                        <a href="{{ route('public.loans.create') }}"
                            class="group flex-1 max-w-[500px] bg-emerald-500/90 border border-emerald-400/80 rounded-2xl px-5 py-5 sm:px-6 sm:py-6 flex flex-col justify-between hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/30">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div
                                    class="w-11 h-11 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7h6l2 4h10m-6 4h6M3 17h6l2-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-base sm:text-lg font-semibold text-white mb-1">
                                        Peminjaman Barang
                                    </h2>
                                    <p class="text-xs sm:text-sm text-emerald-50/95">
                                        Ajukan peminjaman barang / inventaris yang dapat dipinjam.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-xs text-emerald-50/90">
                                <span>Form peminjaman online</span>
                                <span class="inline-flex items-center gap-1">
                                    Ajukan peminjaman
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </main>

            {{-- Footer --}}
            <footer class="py-4 text-center text-[11px] text-slate-500 fade-in-delayed">
                © {{ now()->year }} SEDIA – Sistem Elektronik Data Inventaris & Aset · HRD Hwaseung Indonesia
            </footer>
        </div>
    </div>
</body>

</html>
