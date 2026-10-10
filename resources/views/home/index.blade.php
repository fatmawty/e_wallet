@extends('layouts.app')

@section('title', 'Pemeringkatan E-Wallet Terbaik Mahasiswa | Likert Survey')

@section('content')
<div class="hero-gradient pb-16 pt-8">
    <!-- Hero Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto py-8">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-6">
                <i class="fa-solid fa-square-poll-vertical text-indigo-600"></i> Metodologi Kuesioner Skala Likert (1 - 5)
            </span>
            <h1 class="font-heading font-extrabold text-4xl sm:text-5xl lg:text-6xl text-slate-900 tracking-tight leading-tight mb-6">
                Ranking <span class="bg-clip-text text-transparent bg-gradient-to-r from-forest-600 to-forest-400">E-Wallet Terbaik</span> Pilihan Mahasiswa
            </h1>
            <p class="text-lg text-slate-600 leading-relaxed mb-8">
                Bandingkan GoPay, ShopeePay, OVO, dan DANA berdasarkan penilaian responden pada kriteria keamanan, kemudahan, promo, biaya, dan fitur.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('kuesioner.create') }}" id="btn-hero-survey" class="px-6 py-3.5 rounded-2xl font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Berikan Penilaian Kuesioner
                </a>
                <a href="{{ route('ranking.index') }}" class="px-6 py-3.5 rounded-2xl font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 shadow-sm transition-all hover:-translate-y-0.5">
                    <i class="fa-solid fa-chart-line mr-2 text-indigo-600"></i> Lihat Hasil Rekomendasi
                </a>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-6">
            <div class="glass-card p-5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 text-slate-500 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider">Total Responden</span>
                </div>
                <div class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">
                    {{ $totalRespondents }} <span class="text-xs font-medium text-slate-500 font-sans">Mahasiswa</span>
                </div>
            </div>

            <div class="glass-card p-5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 text-slate-500 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider">Peringkat #1 Likert</span>
                </div>
                <div class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 flex items-center gap-2">
                    {{ $topWallet['name'] ?? 'N/A' }}
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                        {{ $topWallet['avg_score'] }} / 5.0
                    </span>
                </div>
            </div>

            <div class="glass-card p-5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 text-slate-500 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider">Kriteria Evaluasi</span>
                </div>
                <div class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">
                    6 <span class="text-xs font-medium text-slate-500 font-sans">Aspek Likert</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Main Leaderboard Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Leaderboard Resmi</span>
            <h2 class="font-heading font-bold text-2xl sm:text-3xl text-slate-900">
                Pemeringkatan 4 E-Wallet Target
            </h2>
            <p class="text-slate-600 text-sm mt-1">
                Diurutkan berdasarkan skor agregat Skala Likert 1-5 dari seluruh responden.
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-100 p-1.5 rounded-xl border border-slate-200">
            <span class="px-3 py-1 bg-white rounded-lg text-slate-800 shadow-sm font-bold">Skala 1 - 5</span>
            <span class="px-3 py-1">1: Sangat Buruk &rarr; 5: Sangat Baik</span>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
        @foreach($wallets as $wallet)
            <div class="relative bg-white rounded-3xl p-6 border {{ $wallet['rank'] == 1 ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-xl' : 'border-slate-200/80 shadow-sm' }} hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                
                <!-- Rank Badge Top Right -->
                <div class="absolute -top-3 -right-2">
                    @if($wallet['rank'] == 1)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 font-heading font-extrabold text-xs shadow-md shadow-amber-500/30">
                            <i class="fa-solid fa-crown text-slate-950"></i> Rank #1
                        </span>
                    @elseif($wallet['rank'] == 2)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-200 text-slate-700 font-heading font-bold text-xs">
                            Rank #2
                        </span>
                    @elseif($wallet['rank'] == 3)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-700/20 text-amber-900 font-heading font-bold text-xs">
                            Rank #3
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-heading font-semibold text-xs">
                            Rank #4
                        </span>
                    @endif
                </div>

                <div>
                    <!-- Header Wallet Info -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-md" style="background-color: {{ $wallet['color_hex'] }}">
                            {{ substr($wallet['name'], 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xl text-slate-900 group-hover:text-indigo-600 transition-colors">
                                {{ $wallet['name'] }}
                            </h3>
                            <span class="inline-block text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                {{ $wallet['badge_text'] }}
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-2 mb-6 min-h-[32px]">
                        {{ $wallet['tagline'] }}
                    </p>

                    <!-- Score Showcase -->
                    <div class="bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-100">
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-500">Skor Likert Rata-rata</span>
                            <div class="text-right">
                                <span class="font-heading font-extrabold text-2xl text-slate-900">{{ number_format($wallet['avg_score'], 2) }}</span>
                                <span class="text-xs text-slate-400 font-medium">/ 5.0</span>
                            </div>
                        </div>

                        <!-- Star Representation -->
                        <div class="flex items-center justify-between text-xs text-amber-400">
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($wallet['avg_score']))
                                        <i class="fa-solid fa-star"></i>
                                    @elseif($i - $wallet['avg_score'] < 1)
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-star text-slate-300"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Progress preview of criteria -->
                    <div class="space-y-2 mb-6">
                        @foreach($criteria->take(3) as $crit)
                            @php
                                $cScore = $wallet['criterion_scores'][$crit->id] ?? 0;
                                $pct = ($cScore / 5) * 100;
                            @endphp
                            <div>
                                <div class="flex justify-between text-[11px] font-medium text-slate-600 mb-1">
                                    <span class="truncate pr-2">{{ $crit->name }}</span>
                                    <span class="font-semibold text-slate-900">{{ number_format($cScore, 1) }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%; background-color: {{ $wallet['color_hex'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Action Button -->
                <a href="{{ route('ewallet.show', $wallet['slug']) }}" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-center text-slate-700 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200/80 transition-colors flex items-center justify-center gap-1.5">
                    <span>Lihat Detail Breakdown</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>

            </div>
        @endforeach
    </div>

    <!-- Chart & Visual Analysis Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5">
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Visualisasi Radar Chart</span>
                <h3 class="font-heading font-bold text-2xl text-slate-900 mt-1 mb-4">
                    Perbandingan Per Aspek 4 E-Wallet
                </h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">
                    Grafik radar ini memetakan nilai rata-rata Skala Likert pada 6 kriteria utama. E-Wallet dengan area terluas menawarkan performa paling seimbang.
                </p>
                <div class="space-y-3 text-xs">
                    @foreach($wallets as $w)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $w['color_hex'] }}"></span>
                                <span class="font-bold text-slate-800">{{ $w['name'] }}</span>
                            </div>
                            <span class="font-semibold text-slate-600">Skor: {{ number_format($w['avg_score'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-7 flex justify-center items-center relative min-h-[320px]">
                <canvas id="radarChart" class="max-w-md w-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Student Feedback Carousel / Feed -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Suara Mahasiswa</span>
                <h3 class="font-heading font-bold text-2xl text-slate-900">Ulasan Terbaru Responden</h3>
            </div>
            <a href="{{ route('kuesioner.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <span>Tambah Ulasan Anda</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recentRespondents as $resp)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-indigo-200 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-sm text-slate-900 leading-tight">Responden anonim</h4>
                                    <p class="text-[11px] text-slate-500">Mahasiswa</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-slate-600 text-xs italic leading-relaxed mb-4">
                            "{{ $resp->general_feedback }}"
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('radarChart').getContext('2d');

        const criteriaNames = [
            @foreach($criteria as $crit)
                "{{ $crit->name }}",
            @endforeach
        ];

        const datasets = [
            @foreach($wallets as $w)
                {
                    label: "{{ $w['name'] }}",
                    data: [
                        @foreach($criteria as $crit)
                            {{ $w['criterion_scores'][$crit->id] ?? 0 }},
                        @endforeach
                    ],
                    borderColor: "{{ $w['color_hex'] }}",
                    backgroundColor: "{{ $w['color_hex'] }}22",
                    borderWidth: 2,
                    pointBackgroundColor: "{{ $w['color_hex'] }}",
                },
            @endforeach
        ];

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: criteriaNames,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        angleLines: { color: '#e2e8f0' },
                        grid: { color: '#f1f5f9' },
                        suggestedMin: 1,
                        suggestedMax: 5,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        },
                        pointLabels: {
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: '500'
                            },
                            color: '#475569'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Inter', size: 12 },
                            usePointStyle: true,
                            padding: 16
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
