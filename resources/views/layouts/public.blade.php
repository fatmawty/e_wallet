<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK E-Wallet')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Outfit', 'sans-serif'] },
                    colors: { ink: '#172033', mint: '#20b486', coral: '#f26b5b' }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Inter, sans-serif; }
        .font-heading { font-family: Outfit, sans-serif; }
        .public-grid { background-image: linear-gradient(rgba(23,32,51,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(23,32,51,.055) 1px, transparent 1px); background-size: 34px 34px; }
        a, button, label, input, select, textarea { -webkit-tap-highlight-color: transparent; }
        a, button, label { cursor: pointer; }
        a, button { transition: transform 200ms ease, background-color 200ms ease, border-color 200ms ease, color 200ms ease, box-shadow 200ms ease, opacity 200ms ease; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 3px solid rgba(32, 180, 134, .45); outline-offset: 3px; }
        .public-card { transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease; }
        .public-card:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(23, 32, 51, .10); }
        .public-reveal { animation: public-reveal 420ms ease both; }
        .public-reveal-delay { animation-delay: 90ms; }
        .public-pressed:active, a:active, button:active { transform: translateY(1px) scale(.985); }
        @keyframes public-reveal { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body class="min-h-full bg-[#f7faf9] text-ink">
    <header class="sticky top-0 z-30 border-b border-white/70 bg-white/85 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-ink text-white shadow-lg shadow-ink/15"><i class="fa-solid fa-wallet"></i></span>
                <span><strong class="font-heading text-lg">SPK E-Wallet</strong><small class="block text-[10px] font-bold uppercase tracking-[.18em] text-mint">Rekomendasi SAW</small></span>
            </a>
            <nav class="flex items-center gap-2 text-sm font-semibold">
                <a href="{{ route('home') }}" class="hidden rounded-xl px-3 py-2 text-slate-600 hover:bg-slate-100 sm:inline-block {{ request()->routeIs('home', 'public.home') ? 'bg-slate-100 text-ink' : '' }}">Beranda</a>
                <a href="{{ route('kuesioner.create') }}" class="rounded-xl bg-ink px-4 py-2.5 text-white shadow-lg shadow-ink/15 hover:-translate-y-0.5 hover:bg-slate-800 {{ request()->routeIs('kuesioner.*') ? 'ring-2 ring-mint/50 ring-offset-2' : '' }}"><i class="fa-solid fa-pen-to-square mr-2 text-mint"></i>Isi Kuesioner</a>
                <a href="{{ route('ranking.index') }}" class="hidden rounded-xl px-3 py-2 text-slate-600 hover:bg-slate-100 sm:inline-block {{ request()->routeIs('ranking.*') ? 'bg-slate-100 text-ink' : '' }}">Hasil</a>
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 shadow-sm hover:-translate-y-0.5 hover:border-mint hover:text-mint"><i class="fa-solid fa-lock mr-1.5"></i>Login Admin</a>
            </nav>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="border-t border-slate-200 bg-white px-5 py-6 text-center text-xs text-slate-500">
        SPK E-Wallet &bull; 4 alternatif &bull; Metode Simple Additive Weighting
    </footer>
    @yield('scripts')
</body>
</html>
