@extends('layouts.public')

@section('title', 'Rekomendasi E-Wallet Terbaik')

@section('content')
<div class="public-grid overflow-hidden">
    <section class="mx-auto grid max-w-6xl gap-10 px-5 pb-14 pt-16 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:px-8 lg:pt-24">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-mint/20 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-[.16em] text-mint shadow-sm"><i class="fa-solid fa-sparkles"></i> Rekomendasi berbasis respons nyata</span>
            <h1 class="mt-5 max-w-2xl font-heading text-5xl font-extrabold leading-[.98] tracking-tight text-ink sm:text-6xl">Pilih e-wallet dengan lebih yakin.</h1>
            <p class="mt-6 max-w-xl text-base leading-8 text-slate-600">Isi kuesioner singkat untuk ikut memberi penilaian. Sistem mengolah rata-rata respons dan menampilkan rekomendasi paling tinggi untuk empat e-wallet pilihan.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('kuesioner.create') }}" class="rounded-2xl bg-mint px-5 py-3.5 font-bold text-white shadow-xl shadow-mint/20 transition hover:-translate-y-1 hover:bg-emerald-600"><i class="fa-solid fa-arrow-right mr-2"></i> Mulai Menilai</a>
                <a href="{{ route('ranking.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 font-bold text-slate-700 transition hover:-translate-y-1 hover:border-slate-300">Lihat hasil terbaru</a>
            </div>
            <div class="mt-8 flex flex-wrap gap-2 text-xs font-bold text-slate-500">
                @foreach(['GoPay', 'ShopeePay', 'OVO', 'DANA'] as $name)
                    <span class="rounded-full bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200">{{ $name }}</span>
                @endforeach
            </div>
        </div>
        <div class="public-card public-reveal-delay relative rounded-[2rem] border border-white bg-ink p-6 text-white shadow-2xl shadow-slate-900/15 sm:p-8">
            <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-mint/20 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between"><span class="text-xs font-bold uppercase tracking-[.18em] text-slate-400">Rekomendasi saat ini</span><i class="fa-solid fa-chart-simple text-mint"></i></div>
                @if($calcResult['has_data'])
                    <div class="mt-10"><span class="text-sm text-slate-400">Nilai tertinggi</span><h2 class="mt-1 font-heading text-4xl font-extrabold">{{ $calcResult['best_alternative']->name }}</h2><p class="mt-2 font-mono text-mint">V = {{ number_format($calcResult['best_score'], 4) }}</p></div>
                    <div class="mt-8 space-y-3">
                        @foreach($ranking as $row)
                            <div class="flex items-center gap-3"><span class="w-5 text-xs font-bold text-slate-400">#{{ $row['rank'] }}</span><div class="h-2 flex-1 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-mint" style="width: {{ max(8, min(100, $row['score'] * 100)) }}%"></div></div><span class="w-14 text-right font-mono text-xs text-slate-300">{{ number_format($row['score'], 4) }}</span></div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-10"><h2 class="font-heading text-3xl font-extrabold">Menunggu respons</h2><p class="mt-3 text-sm leading-6 text-slate-400">Lengkapi kuesioner untuk menampilkan rekomendasi dan ranking.</p></div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
