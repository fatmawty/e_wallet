@extends('layouts.public')

@section('title', 'Penelitian SPK E-Wallet untuk Mahasiswa | Metode SAW')

@section('content')
@php
    $criterionPresentation = [
        'C1' => ['icon' => 'fa-shield-halved', 'group' => 'Kepercayaan'],
        'C2' => ['icon' => 'fa-wand-magic-sparkles', 'group' => 'Pengalaman'],
        'C3' => ['icon' => 'fa-tags', 'group' => 'Manfaat'],
        'C4' => ['icon' => 'fa-coins', 'group' => 'Manfaat'],
        'C5' => ['icon' => 'fa-layer-group', 'group' => 'Pengalaman'],
        'C6' => ['icon' => 'fa-arrow-right-arrow-left', 'group' => 'Pengalaman'],
        'C7' => ['icon' => 'fa-store', 'group' => 'Jangkauan'],
    ];
    $progress = min(100, (int) round(($respondentCount / $respondentTarget) * 100));
@endphp

<div class="overflow-hidden bg-sand-50">
    <section class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_10%_0%,rgba(244,177,59,.15),transparent_38%),linear-gradient(180deg,#FBF7F1_0%,#F2E6D6_100%)]"></div>
        <div class="public-grid absolute inset-0 -z-10 opacity-50"></div>
        <div class="mx-auto grid max-w-7xl gap-12 px-5 pb-16 pt-12 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:gap-16 lg:pb-24 lg:pt-20">
            <div class="public-reveal">
                <span class="inline-flex items-center gap-2 rounded-full border border-forest-200 bg-white/75 px-3.5 py-2 text-[11px] font-extrabold uppercase tracking-[.16em] text-forest-700 shadow-sm backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-gold-400"></span>
                    Penelitian mahasiswa &bull; SPK metode SAW
                </span>
                <h1 class="mt-7 max-w-3xl font-heading text-5xl font-extrabold leading-[.98] tracking-tight text-forest-700 sm:text-6xl lg:text-7xl">
                    E-Wallet Terbaik Menurut <span class="text-clay-500">Penilaian Mahasiswa</span>
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                    Bantu penelitian ini dengan memberi tingkat kepentingan pada kriteria dan menilai empat alternatif e-wallet. Bobot serta peringkat dihitung dari jawaban responden—bukan penilaian admin.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('kuesioner.create') }}" class="glass-button inline-flex items-center justify-center gap-2.5 rounded-2xl px-6 py-4 text-sm font-extrabold transition duration-300 hover:-translate-y-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gold-600">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Ikut mengisi kuesioner</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ route('ranking.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-forest-200 bg-white/70 px-5 py-4 text-sm font-bold text-forest-700 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-forest-400 hover:bg-white">
                        Lihat hasil penelitian
                        <i class="fa-solid fa-chart-column text-xs"></i>
                    </a>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-2"><i class="fa-regular fa-clock text-forest-600"></i> Isi dengan santai sesuai pengalaman dan pengetahuan</span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-user-graduate text-forest-600"></i> Untuk mahasiswa aktif</span>
                </div>
            </div>

            <div class="public-card public-reveal public-reveal-delay relative">
                <div class="glass-surface rounded-[2rem] p-6 text-white sm:p-8 lg:rotate-1">
                    <div class="relative">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-[.2em] text-gold-300">Riset yang sedang berjalan</span>
                                <h2 class="mt-2 font-heading text-2xl font-bold">Empat alternatif.</h2>
                                <p class="mt-1 text-sm text-white/75">Satu perbandingan berbasis kriteria.</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-gold-300 shadow-inner backdrop-blur">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>
                        </div>

                        <div class="mt-7 grid grid-cols-2 gap-3">
                            @foreach($wallets as $wallet)
                                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-3.5 shadow-lg shadow-forest-950/10 backdrop-blur-md">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-extrabold text-white shadow-md" style="background-color: {{ $wallet->color_hex ?? '#0F3D3A' }}">
                                        {{ strtoupper(substr($wallet->name, 0, 1)) }}
                                    </span>
                                    <span class="text-sm font-bold">{{ $wallet->name }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 rounded-2xl border border-white/15 bg-forest-950/25 p-4 backdrop-blur-md">
                            <div class="flex items-end justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-[.15em] text-white/60">Respons masuk</span>
                                    <p class="mt-1 font-heading text-3xl font-extrabold">{{ $respondentCount }} <span class="text-base font-semibold text-white/55">/ {{ $respondentTarget }}</span></p>
                                </div>
                                <span class="rounded-full border border-gold-300/30 bg-gold-400/10 px-3 py-1.5 text-[10px] font-bold text-gold-200">
                                    {{ $respondentCount >= $respondentTarget ? 'Target tercapai' : 'Target rencana' }}
                                </span>
                            </div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/15">
                                <div class="h-full rounded-full bg-gradient-to-r from-gold-400 via-[#F8D477] to-clay-300 transition-all duration-700" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-between text-[10px] text-white/55">
                                <span>Partisipasi mahasiswa</span>
                                <span>{{ $progress }}% dari target rencana</span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[.07] px-4 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gold-400/15 text-gold-300">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </span>
                            <p class="text-xs leading-5 text-white/75">
                                Jawaban kepentingan membentuk <strong class="text-white">bobot</strong>; penilaian e-wallet membentuk <strong class="text-white">matriks SAW</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-5 -left-4 -z-10 h-24 w-24 rounded-full bg-clay-400/30 blur-3xl"></div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div class="max-w-2xl">
                <span class="text-[11px] font-extrabold uppercase tracking-[.2em] text-clay-600">Peta penilaian</span>
                <h2 class="mt-3 font-heading text-3xl font-extrabold tracking-tight text-forest-700 sm:text-4xl">Apa yang akan kamu nilai?</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">Setiap kriteria dinilai dua kali: tingkat kepentingannya bagi kamu dan performa setiap alternatif menurut persepsi kamu.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-sand-300 bg-white/80 px-3.5 py-2 text-xs font-bold text-slate-600">
                <i class="fa-solid fa-list-check text-forest-600"></i> {{ $criteria->count() }} kriteria
            </span>
        </div>

        <div id="criteria" class="mt-8 grid scroll-mt-24 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($criteria as $criterion)
                @php $presentation = $criterionPresentation[$criterion->code] ?? ['icon' => 'fa-circle-check', 'group' => 'Kriteria']; @endphp
                <article class="rounded-2xl border border-sand-300/70 bg-white/80 p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-forest-200 hover:shadow-lg hover:shadow-forest-900/5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest-50 text-forest-600">
                            <i class="fa-solid {{ $presentation['icon'] }}"></i>
                        </span>
                        <span class="rounded-full bg-sand-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-sand-700">{{ $criterion->code }}</span>
                    </div>
                    <p class="mt-4 text-[10px] font-extrabold uppercase tracking-[.16em] text-clay-600">{{ $presentation['group'] }}</p>
                    <h3 class="mt-1 font-heading text-base font-bold text-forest-800">{{ $criterion->name }}</h3>
                    @if($criterion->description)
                        <p class="mt-2 text-xs leading-5 text-slate-500">{{ $criterion->description }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="border-y border-sand-300/70 bg-white/60">
        <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-[.75fr_1.25fr] lg:items-center">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-[.2em] text-forest-600">Dari jawaban ke peringkat</span>
                    <h2 class="mt-3 font-heading text-3xl font-extrabold tracking-tight text-forest-700 sm:text-4xl">Begini alur SAW bekerja.</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">Hasil bukan iklan atau klaim universal. Ini adalah pemeringkatan yang dihitung dari bobot kriteria dan penilaian responden yang masuk.</p>
                    <a href="{{ route('about') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-forest-700 hover:text-clay-600">
                        Lihat kriteria penelitian
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach([
                        ['number' => '01', 'icon' => 'fa-sliders', 'title' => 'Tentukan bobot', 'text' => 'Jawaban tingkat kepentingan dirata-ratakan dan dinormalisasi menjadi bobot.'],
                        ['number' => '02', 'icon' => 'fa-table-cells', 'title' => 'Nilai alternatif', 'text' => 'Penilaian responden dirata-ratakan untuk setiap e-wallet dan kriteria.'],
                        ['number' => '03', 'icon' => 'fa-ranking-star', 'title' => 'Hitung ranking', 'text' => 'SAW menormalisasi nilai, mengalikan bobot, lalu mengurutkan hasilnya.'],
                    ] as $step)
                        <article class="rounded-2xl border border-sand-300/70 bg-sand-50 p-5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold tracking-widest text-clay-600">{{ $step['number'] }}</span>
                                <i class="fa-solid {{ $step['icon'] }} text-forest-600"></i>
                            </div>
                            <h3 class="mt-5 font-heading font-bold text-forest-800">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-xs leading-5 text-slate-600">{{ $step['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <span class="text-[11px] font-extrabold uppercase tracking-[.2em] text-clay-600">Hasil berbasis data</span>
                <h2 class="mt-3 font-heading text-3xl font-extrabold tracking-tight text-forest-700 sm:text-4xl">Peringkat respons saat ini.</h2>
            </div>
            <a href="{{ route('ranking.index') }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-forest-200 bg-white px-4 py-2.5 text-xs font-bold text-forest-700 transition hover:border-forest-500 hover:bg-forest-50">
                Buka halaman hasil
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>

        @if($calcResult['has_data'])
            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($ranking as $row)
                    <article class="public-card rounded-3xl border {{ $row['rank'] === 1 ? 'border-gold-300 bg-gradient-to-br from-white via-gold-50 to-sand-100 shadow-lg shadow-gold-600/10' : 'border-sand-300/70 bg-white/85 shadow-sm' }} p-5">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-[.18em] {{ $row['rank'] === 1 ? 'text-clay-600' : 'text-slate-400' }}">Peringkat {{ str_pad((string) $row['rank'], 2, '0', STR_PAD_LEFT) }}</span>
                            @if($row['rank'] === 1)
                                <i class="fa-solid fa-crown text-gold-500"></i>
                            @endif
                        </div>
                        <div class="mt-5 flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl text-lg font-extrabold text-white shadow-md" style="background-color: {{ $row['alternative']->brand_color ?? '#0F3D3A' }}">
                                {{ strtoupper(substr($row['alternative']->name, 0, 1)) }}
                            </span>
                            <div>
                                <h3 class="font-heading text-lg font-bold text-forest-800">{{ $row['alternative']->name }}</h3>
                                <p class="text-[11px] text-slate-500">Nilai preferensi SAW</p>
                            </div>
                        </div>
                        <div class="mt-5 flex items-end justify-between">
                            <span class="font-mono text-2xl font-bold text-forest-700">{{ number_format($row['score'], 4) }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">V<sub>i</sub></span>
                        </div>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-sand-200">
                            <div class="h-full rounded-full {{ $row['rank'] === 1 ? 'bg-gradient-to-r from-gold-400 to-clay-400' : 'bg-gradient-to-r from-forest-500 to-forest-300' }}" style="width: {{ max(5, min(100, $row['score'] * 100)) }}%"></div>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-slate-500">Dihitung dari {{ $calcResult['respondent_count'] }} respons lengkap. Ranking diperbarui saat halaman hasil dibuka.</p>
        @else
            <div class="glass-surface mt-8 rounded-3xl p-6 text-white sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-8">
                <div class="relative">
                    <span class="text-[10px] font-extrabold uppercase tracking-[.2em] text-gold-300">Belum ada ranking</span>
                    <h3 class="mt-2 font-heading text-2xl font-extrabold">Jadi bagian dari data pertama.</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-white/75">Peringkat akan muncul setelah tersedia penilaian lengkap untuk tingkat kepentingan dan semua alternatif.</p>
                </div>
                <a href="{{ route('kuesioner.create') }}" class="glass-button relative mt-5 inline-flex shrink-0 items-center gap-2 rounded-xl px-4 py-3 text-sm font-extrabold sm:mt-0">
                    <span>Mulai survei</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-5 pb-16 sm:px-8 lg:pb-20">
        <div class="grid gap-5 lg:grid-cols-[1.2fr_.8fr]">
            <div class="rounded-3xl border border-forest-200/80 bg-forest-50/80 p-6 sm:p-8">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-forest-600 text-white"><i class="fa-solid fa-user-shield"></i></span>
                <h2 class="mt-4 font-heading text-xl font-bold text-forest-800">Partisipasi dan privasi.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Survei ditujukan untuk mahasiswa aktif dan bersifat sukarela. Nama disimpan untuk peneliti/admin, tidak ditampilkan pada halaman publik, dan harus dianonimkan sebelum hasil penelitian dibagikan.</p>
            </div>
            <div class="rounded-3xl border border-gold-200 bg-gradient-to-br from-gold-50 via-white to-sand-100 p-6 sm:p-8">
                <span class="text-[10px] font-extrabold uppercase tracking-[.2em] text-clay-600">Sebelum mulai</span>
                <h2 class="mt-3 font-heading text-xl font-bold text-forest-800">Jawab sesuai yang kamu ketahui.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Penilaian alternatif mencerminkan pengalaman atau pengetahuanmu. Skala rendah berarti penilaian rendah—bukan jawaban “belum pernah memakai”.</p>
                <a href="{{ route('kuesioner.create') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold text-forest-700 hover:text-clay-600">
                    Lanjut ke kuesioner
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
