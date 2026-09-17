<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pendukung Keputusan (SPK) Pemilihan E-Wallet Terbaik dengan Metode Simple Additive Weighting (SAW).">
    <title>@yield('title', 'SPK Pemilihan E-Wallet Terbaik - Metode SAW')</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#1e1b4b',
                        },
                        dana: '#118EEA',
                        ovo: '#4C2A86',
                        shopeepay: '#EE4D2D',
                        gopay: '#00AA13',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .sidebar-active {
            background-color: #059669;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-white to-slate-100 text-slate-800 font-sans antialiased flex flex-col md:flex-row min-h-screen">

    <!-- Mobile Top Header Bar -->
    <div class="md:hidden sticky top-0 z-40 bg-slate-900 text-white px-4 py-3 flex items-center justify-between shadow-md">
        <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white shadow-sm">
                <i class="fa-solid fa-wallet text-sm"></i>
            </div>
            <div>
                <span class="font-heading font-bold text-base tracking-tight text-white">SPK <span class="text-emerald-400">E-Wallet</span></span>
                <span class="text-[10px] block text-slate-400 font-medium">Simple Additive Weighting</span>
            </div>
        </a>
        <button id="mobile-menu-toggle" type="button" class="p-2 text-slate-300 hover:text-white focus:outline-none" onclick="toggleMobileSidebar()">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Backdrop for Mobile Sidebar -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm hidden md:hidden" onclick="toggleMobileSidebar()"></div>

    <!-- SIDEBAR NAVIGATION -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto flex flex-col transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-xl md:shadow-none">
        
        <!-- Sidebar Brand Logo Header -->
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('dashboard') : route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-600/30">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading font-extrabold text-lg text-white tracking-tight">SPK <span class="text-emerald-400">E-Wallet</span></h1>
                    <span class="text-[11px] font-semibold tracking-wider text-emerald-300 uppercase block">Metode SAW</span>
                </div>
            </a>
            <button class="md:hidden text-slate-400 hover:text-white" onclick="toggleMobileSidebar()">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Sidebar Menu Links -->
        <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto custom-scrollbar font-medium text-sm">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</div>
            
            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center text-emerald-400"></i>
                <span>Dashboard</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('criteria.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('criteria.*') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-sliders w-5 text-center text-sky-400"></i>
                <span>Kriteria</span>
            </a>

            <a href="{{ route('alternatives.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('alternatives.*') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-wallet w-5 text-center text-amber-400"></i>
                <span>Alternatif E-Wallet</span>
            </a>
            <a href="{{ route('respondents.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('respondents.*') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-users w-5 text-center text-sky-400"></i>
                <span>Data Responden</span>
            </a>
            @endif

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Hitung & Rekomendasi</div>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('saw.process') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('saw.*') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-calculator w-5 text-center text-emerald-400"></i>
                <span>Proses SAW</span>
            </a>
            @endif

            <a href="{{ route('ranking.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('ranking.*') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-trophy w-5 text-center text-amber-400"></i>
                <span>Hasil Ranking</span>
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Informasi</div>

            <a href="{{ route('about') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('about') ? 'sidebar-active font-semibold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-circle-info w-5 text-center text-slate-400"></i>
                <span>Tentang Sistem</span>
            </a>
            @endif
        </nav>

        <!-- Sidebar Footer Status -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40 text-xs text-slate-500">
            <div class="flex items-center justify-between mb-1">
                <span class="font-semibold text-slate-400">Metode SAW:</span>
                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active
                </span>
            </div>
            <p class="text-[11px] text-slate-500">GoPay, ShopeePay, OVO, DANA</p>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navbar Header -->
        <header class="bg-white/60 backdrop-blur-md border-b border-white/80 px-4 sm:px-8 py-4 flex items-center justify-between sticky top-0 z-30 shadow-xl shadow-slate-200/60">
            <div>
                <h2 class="font-heading font-extrabold text-xl text-slate-900 tracking-tight">
                    @yield('page-title', 'Dashboard SPK E-Wallet')
                </h2>
                <p class="text-xs text-slate-500 hidden sm:block">
                    @yield('page-subtitle', 'Sistem Pendukung Keputusan Pemilihan E-Wallet Terbaik Menggunakan Metode SAW')
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden lg:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                    <i class="fa-solid fa-microchip text-emerald-600"></i> Engine: Simple Additive Weighting
                </span>
                
                @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('saw.process') }}" class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-2xl shadow-emerald-600/30 transition-all">
                    <i class="fa-solid fa-play text-xs"></i>
                    <span>Hitung SAW</span>
                </a>
                @endif
                @auth
                <form method="POST" action="{{ route('logout') }}" class="fixed bottom-6 left-6 z-40">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xl transition-all">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
                @endauth
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="px-4 sm:px-8 pt-6">
            @if(session('success'))
                <div class="p-4 mb-4 text-emerald-800 border border-emerald-200 rounded-2xl bg-emerald-50/90 shadow-xs flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-3 text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-circle-check text-lg text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="text-emerald-500 hover:text-emerald-800 text-sm" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 mb-4 text-amber-800 border border-amber-200 rounded-2xl bg-amber-50/90 shadow-xs flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-3 text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-triangle-exclamation text-lg text-amber-600"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="text-amber-500 hover:text-amber-800 text-sm" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-rose-800 border border-rose-200 rounded-2xl bg-rose-50/90 shadow-xs" role="alert">
                    <div class="flex items-center gap-2 text-sm font-bold mb-1">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Terdapat kesalahan pada input Anda:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Body Page Content -->
        <main class="flex-1 px-4 sm:px-8 py-4 pb-16">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200/80 px-4 sm:px-8 py-4 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
            <div>
                <span class="font-semibold text-slate-700">SPK E-Wallet Terbaik &copy; {{ date('Y') }}</span> &bull; Metode Simple Additive Weighting (SAW)
            </div>
            <div class="flex items-center gap-3 text-[11px]">
                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 font-medium">GoPay &bull; ShopeePay &bull; OVO &bull; DANA</span>
            </div>
        </footer>

    </div>

    <!-- Toggle Mobile Sidebar Script -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>

    @yield('scripts')
</body>
</html>
