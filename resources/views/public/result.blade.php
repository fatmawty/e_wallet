@extends('layouts.public')

@section('title', 'Hasil Rekomendasi E-Wallet')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-12 lg:px-8">
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</div>
    @endif
    <div class="mb-10 max-w-2xl"><span class="text-xs font-bold uppercase tracking-[.18em] text-mint">Hasil kuesioner</span><h1 class="mt-3 font-heading text-4xl font-extrabold tracking-tight text-ink sm:text-5xl">Rekomendasi untuk Anda</h1><p class="mt-4 leading-7 text-slate-600">Nilai di bawah adalah hasil agregasi respons kuesioner dan pemeringkatan empat e-wallet. Detail rumus SAW tersedia khusus untuk Admin.</p></div>

    @if(!$calcResult['has_data'])
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-900"><strong>Hasil belum tersedia.</strong><p class="mt-1 text-sm">{{ $calcResult['message'] }}</p><a href="{{ route('kuesioner.create') }}" class="mt-5 inline-flex rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white">Isi kuesioner</a></div>
    @else
        <div class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
            <section class="public-card public-reveal rounded-[2rem] bg-ink p-7 text-white shadow-2xl shadow-slate-900/15 sm:p-9"><span class="text-xs font-bold uppercase tracking-[.18em] text-gold-300">Rekomendasi utama</span><h2 class="mt-6 font-heading text-4xl font-extrabold">{{ $calcResult['best_alternative']->name }}</h2><p class="mt-3 text-sm leading-6 text-slate-300">Alternatif ini memperoleh nilai preferensi tertinggi dari perhitungan sistem.</p><div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5"><span class="text-xs uppercase tracking-wider text-slate-300">Nilai preferensi V</span><strong class="mt-1 block font-mono text-3xl text-gold-300">{{ number_format($calcResult['best_score'], 4) }}</strong></div><a href="{{ route('kuesioner.create') }}" class="mt-6 inline-flex rounded-xl bg-gold-400 px-4 py-3 text-sm font-bold text-forest-950 hover:bg-gold-300">Isi ulang kuesioner</a></section>
            <section class="public-card public-reveal public-reveal-delay rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8"><div class="flex items-end justify-between"><div><span class="text-xs font-bold uppercase tracking-[.18em] text-slate-400">Ranking 4 e-wallet</span><h2 class="mt-2 font-heading text-2xl font-bold text-ink">Nilai preferensi</h2></div><i class="fa-solid fa-ranking-star text-2xl text-coral"></i></div><div class="mt-7 space-y-4">@foreach($ranking as $row)<div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-xl {{ $row['rank'] === 1 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }} text-sm font-extrabold">{{ $row['rank'] }}</span><div class="min-w-0 flex-1"><div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-bold text-ink">{{ $row['alternative']->name }}</span><span class="font-mono text-slate-500">{{ number_format($row['score'], 4) }}</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $row['rank'] === 1 ? 'bg-mint' : 'bg-slate-300' }} transition-all duration-700" style="width: {{ max(8, min(100, $row['score'] * 100)) }}%"></div></div></div></div>@endforeach</div></section>
        </div>
        <section class="public-card public-reveal mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8"><div class="mb-5 flex items-center justify-between"><div><span class="text-xs font-bold uppercase tracking-[.18em] text-slate-400">Visualisasi</span><h2 class="mt-2 font-heading text-2xl font-bold text-ink">Grafik ranking</h2></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Dihitung otomatis</span></div><div class="relative h-72"><canvas id="publicRankingChart" aria-label="Grafik nilai preferensi e-wallet" role="img"></canvas></div></section>
    @endif
</div>
@endsection

@section('scripts')
@if($calcResult['has_data'])
<script>
    new Chart(document.getElementById('publicRankingChart'), {
        type: 'bar',
        data: { labels: @json(array_map(fn($row) => $row['alternative']->name, $ranking)), datasets: [{ label: 'Nilai Preferensi V', data: @json(array_map(fn($row) => $row['score'], $ranking)), backgroundColor: ['#0F3D3A', '#F4B13B', '#E0745E', '#D2E3E0'], borderRadius: 10, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip: { enabled: true, callbacks: { label: context => ` Nilai V: ${Number(context.raw).toFixed(4)}` } } }, scales: { y: { beginAtZero: true, suggestedMax: 1, grid: { color: '#e2e8f0' } }, x: { grid: { display: false } } } }
    });
</script>
@endif
@endsection
