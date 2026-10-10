<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK E-Wallet')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        const forest = {
            50: '#EAF2F0',
            100: '#D2E3E0',
            200: '#A6C7C1',
            300: '#79AAA2',
            400: '#4D8E83',
            500: '#2D7068',
            600: '#0F3D3A',
            700: '#0B302E',
            800: '#082523',
            900: '#061C1B',
            950: '#031211',
        };
        const goldenHour = {
            50: '#FFF8E8',
            100: '#FCECC9',
            200: '#F8D998',
            300: '#F5C667',
            400: '#F4B13B',
            500: '#E69B22',
            600: '#C27A16',
            700: '#985812',
            800: '#70400F',
            900: '#4A2A0B',
            950: '#2B1807',
        };
        const terracotta = {
            50: '#FCF0ED',
            100: '#F8DDD6',
            200: '#F1BBAE',
            300: '#E99A87',
            400: '#E0745E',
            500: '#C95D48',
            600: '#A94332',
            700: '#813326',
            800: '#5F281F',
            900: '#3F1C17',
            950: '#26110E',
        };

        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Outfit', 'sans-serif'] },
                    colors: {
                        forest,
                        gold: goldenHour,
                        clay: terracotta,
                        sand: {
                            50: '#FBF7F1',
                            100: '#F2E6D6',
                            200: '#E7D2B8',
                            300: '#D7B995',
                            400: '#C6A078',
                            500: '#A9815D',
                            600: '#866448',
                            700: '#654B38',
                            800: '#48362B',
                            900: '#30241D',
                        },
                        indigo: forest,
                        blue: forest,
                        sky: forest,
                        cyan: forest,
                        emerald: forest,
                        green: forest,
                        teal: forest,
                        violet: forest,
                        purple: forest,
                        amber: goldenHour,
                        yellow: goldenHour,
                        orange: terracotta,
                        rose: terracotta,
                        red: terracotta,
                        ink: '#0F3D3A',
                        mint: '#0F3D3A',
                        coral: '#E0745E',
                    }
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
        .public-grid { background-image: linear-gradient(rgba(15,61,58,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(15,61,58,.055) 1px, transparent 1px); background-size: 34px 34px; }
        a, button, label, input, select, textarea { -webkit-tap-highlight-color: transparent; }
        a, button, label { cursor: pointer; }
        a, button { transition: transform 200ms ease, background-color 200ms ease, border-color 200ms ease, color 200ms ease, box-shadow 200ms ease, opacity 200ms ease; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 3px solid rgba(244, 177, 59, .7); outline-offset: 3px; }
        .public-card { transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease; }
        .public-card:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(15, 61, 58, .12); }
        .glass-surface {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 92% 4%, rgba(244, 177, 59, .24), transparent 34%),
                linear-gradient(135deg, #0F3D3A 0%, #17645B 52%, #082523 100%);
            border: 1px solid rgba(255, 255, 255, .38);
            box-shadow: 0 28px 70px rgba(15, 61, 58, .23), inset 0 1px 0 rgba(255, 255, 255, .38);
        }
        .glass-surface::before {
            position: absolute;
            z-index: -1;
            top: -55%;
            left: -15%;
            width: 85%;
            height: 90%;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 255, 255, .28), rgba(255, 255, 255, 0));
            content: "";
            filter: blur(18px);
            pointer-events: none;
        }
        .glass-button {
            position: relative;
            overflow: hidden;
            background: linear-gradient(115deg, #F4B13B 0%, #F8D477 48%, #E99A42 100%);
            box-shadow: 0 14px 30px rgba(194, 122, 22, .22), inset 0 1px 0 rgba(255, 255, 255, .72);
            color: #0F3D3A;
        }
        .glass-button::after {
            position: absolute;
            top: -70%;
            left: -30%;
            width: 45%;
            height: 240%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .55), transparent);
            content: "";
            transform: rotate(24deg);
            transition: left 450ms ease;
        }
        .glass-button:hover::after { left: 110%; }
        .glass-button > * { position: relative; z-index: 1; }
        .public-reveal { animation: public-reveal 420ms ease both; }
        .public-reveal-delay { animation-delay: 90ms; }
        .public-pressed:active, a:active, button:active { transform: translateY(1px) scale(.985); }
        @keyframes public-reveal { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body class="min-h-full bg-sand-100 text-ink">
    <header class="sticky top-0 z-30 border-b border-white/70 bg-white/85 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-ink text-white shadow-lg shadow-ink/15"><i class="fa-solid fa-wallet"></i></span>
                <span><strong class="font-heading text-lg">SPK E-Wallet</strong><small class="block text-[10px] font-bold uppercase tracking-[.18em] text-forest-600">Rekomendasi SAW</small></span>
            </a>
            <nav class="flex items-center gap-2 text-sm font-semibold">
                <a href="{{ route('home') }}" class="hidden rounded-xl px-3 py-2 text-slate-600 hover:bg-slate-100 sm:inline-block {{ request()->routeIs('home', 'public.home') ? 'bg-slate-100 text-ink' : '' }}">Beranda</a>
                <a href="{{ route('kuesioner.create') }}" class="rounded-xl bg-ink px-4 py-2.5 text-white shadow-lg shadow-ink/15 hover:-translate-y-0.5 hover:bg-forest-700 {{ request()->routeIs('kuesioner.*') ? 'ring-2 ring-gold-400/70 ring-offset-2' : '' }}"><i class="fa-solid fa-pen-to-square mr-2 text-gold-400"></i>Isi Kuesioner</a>
                <a href="{{ route('ranking.index') }}" class="hidden rounded-xl px-3 py-2 text-slate-600 hover:bg-slate-100 sm:inline-block {{ request()->routeIs('ranking.*') ? 'bg-slate-100 text-ink' : '' }}">Hasil</a>
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 shadow-sm hover:-translate-y-0.5 hover:border-forest-300 hover:text-forest-700"><i class="fa-solid fa-lock mr-1.5"></i>Login Admin</a>
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
