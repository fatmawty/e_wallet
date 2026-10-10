@extends('layouts.app')

@section('title', 'Dashboard - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Dashboard SPK E-Wallet')
@section('page-subtitle', 'Temukan e-wallet yang paling sesuai berdasarkan kriteria dan bobot penilaian.')

@section('content')
<div class="space-y-8">

    @if(!$calcResult['has_data'])
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
            {{ $calcResult['message'] }}
        </div>
    @endif

    <!-- SURVEY CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Partisipasi Anda</span>
            <p class="text-sm text-slate-500 mt-1">Bantu lengkapi data penilaian e-wallet.</p>
        </div>
        <a href="{{ $googleFormUrl ?: url('/kuesioner') }}"
           target="_blank"
           rel="noopener noreferrer"
           class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-pen-to-square"></i>
            Isi Kuesioner Sekarang
        </a>
    </div>

    <!-- SUMMARY STATISTICS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Responden</span>
                    <span class="text-xs text-slate-500 font-medium">Pengisi kuesioner</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-3xl text-slate-950 mt-3">
                {{ $totalRespondents }}
            </div>
        </div>

        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">E-Wallet Dibandingkan</span>
                    <span class="text-xs text-slate-500 font-medium">GoPay, ShopeePay, OVO, DANA</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-3xl text-slate-950 mt-3">
                {{ $totalComparedWallets }}
            </div>
        </div>
    </div>
    
    <!-- STATISTIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Jumlah Alternatif -->
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60 hover:shadow-2xl transition-all">
            <div class="flex items-center gap-3.5 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Alternatif</span>
                    <span class="text-xs text-slate-500 font-medium">E-Wallet Target</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-3xl text-slate-950">
                {{ $totalAlternatives }} <span class="text-xs font-semibold text-slate-400 font-sans">E-Wallet</span>
            </div>
        </div>

        <!-- Card 2: Jumlah Kriteria -->
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60 hover:shadow-2xl transition-all">
            <div class="flex items-center gap-3.5 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Kriteria</span>
                    <span class="text-xs text-slate-500 font-medium">Penilaian Terbobot</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-3xl text-slate-950">
                {{ $totalCriteria }} <span class="text-xs font-semibold text-slate-400 font-sans">Dimensi</span>
            </div>
        </div>

        <!-- Card 3: Metode SAW -->
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60 hover:shadow-2xl transition-all">
            <div class="flex items-center gap-3.5 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Metode</span>
                    <span class="text-xs text-slate-500 font-medium">Algoritma SPK</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-2xl text-slate-950 tracking-tight">
                SAW <span class="text-xs font-semibold text-emerald-600 block font-sans">Additive Weighting</span>
            </div>
        </div>

        <!-- Card 4: E-Wallet Terbaik -->
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-5 border border-white/80 shadow-xl shadow-slate-200/60 hover:shadow-2xl transition-all">
            <div class="flex items-center gap-3.5 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">E-Wallet Terbaik</span>
                    <span class="text-xs text-slate-500 font-medium">Peringkat #1</span>
                </div>
            </div>
            <div class="font-heading font-extrabold text-2xl text-emerald-600 truncate">
                {{ $calcResult['best_alternative']->name ?? 'N/A' }}
                <span class="text-xs font-bold text-slate-500 block font-sans">Skor: {{ number_format($calcResult['best_score'] ?? 0, 4) }}</span>
            </div>
        </div>
    </div>

    <!-- MAIN RECOMMENDATION CARD -->
    @if(isset($calcResult['best_alternative']))
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-6 sm:p-8 text-slate-800 border border-white/80 shadow-xl shadow-slate-200/60 relative overflow-hidden">
            <!-- Subtle background accent circles -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-white font-heading font-extrabold text-4xl shadow-xl shrink-0" style="background-color: {{ $calcResult['best_alternative']->brand_color }}">
                        {{ substr($calcResult['best_alternative']->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-extrabold tracking-wider uppercase">
                                <i class="fa-solid fa-trophy text-emerald-600"></i> Rekomendasi Utama (Peringkat #1)
                            </span>
                        </div>
                        <h3 class="font-heading font-extrabold text-3xl text-slate-950 mb-2">
                            {{ $calcResult['best_alternative']->name }}
                        </h3>
                        <p class="text-slate-600 text-sm max-w-xl leading-relaxed">
                            <strong>{{ $calcResult['best_alternative']->name }}</strong> memperoleh nilai tertinggi sebesar 
                            <span class="text-emerald-700 font-bold font-mono text-base px-1.5 py-0.5 rounded bg-emerald-50 border border-emerald-200">{{ number_format($calcResult['best_score'], 4) }}</span>
                            berdasarkan pembobotan kriteria dan penilaian yang digunakan.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    <a href="{{ route('ranking.index') }}" class="px-6 py-3.5 rounded-2xl font-bold text-sm text-slate-900 bg-white hover:bg-slate-100 shadow-lg transition-all text-center">
                        <i class="fa-solid fa-trophy text-emerald-600 mr-2"></i> Lihat Hasil Lengkap
                    </a>
                    <a href="{{ route('saw.process') }}" class="px-5 py-3.5 rounded-2xl font-semibold text-sm text-slate-700 bg-white/70 hover:bg-white border border-white/80 transition-all text-center">
                        <i class="fa-solid fa-calculator text-emerald-600 mr-2"></i> Detail SAW
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- GRAPH & SUMMARY TABLE SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Ranking Bar Chart -->
        <div class="lg:col-span-7 bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/80 shadow-xl shadow-slate-200/60 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Visualisasi Nilai Preferensi</span>
                        <h4 class="font-heading font-bold text-xl text-slate-950">Grafik Ranking SAW ($V_i$)</h4>
                    </div>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">Nilai Max: 1.0000</span>
                </div>
                <p class="text-slate-500 text-xs mb-6">
                    Grafik batang menunjukkan skor preferensi akhir hasil perhitungan metode Simple Additive Weighting (SAW).
                </p>
            </div>

            <div class="relative h-64 sm:h-72 w-full flex items-center justify-center">
                <canvas id="rankingChart"></canvas>
            </div>
        </div>

        <!-- Summary Ranking Table -->
        <div class="lg:col-span-5 bg-white/60 backdrop-blur-md rounded-3xl p-6 border border-white/80 shadow-xl shadow-slate-200/60 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Ringkasan Perangkingan</span>
                        <h4 class="font-heading font-bold text-xl text-slate-950">Top 4 E-Wallet</h4>
                    </div>
                    <a href="{{ route('ranking.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                        Selengkapnya &rarr;
                    </a>
                </div>

                <div class="space-y-3 mb-6">
                    @foreach($results as $res)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl border {{ $res->rank == 1 ? 'border-emerald-200 bg-emerald-50/50' : 'border-slate-100 bg-slate-50/60' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-xl flex items-center justify-center font-heading font-extrabold text-xs {{ $res->rank == 1 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">
                                    #{{ $res->rank }}
                                </div>
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-xs" style="background-color: {{ $res->alternative->brand_color }}">
                                    {{ substr($res->alternative->name, 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="font-bold text-sm text-slate-950 leading-tight">{{ $res->alternative->name }}</h5>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ $res->alternative->code }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-heading font-extrabold text-base text-slate-950 block">{{ number_format($res->final_score, 4) }}</span>
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Skor SAW</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('ranking.index') }}" class="w-full py-3 rounded-xl font-bold text-xs text-center text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-colors block">
                <i class="fa-solid fa-trophy mr-1.5 text-emerald-600"></i> Buka Halaman Hasil Rekomendasi
            </a>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rankingChart').getContext('2d');

        const labels = [
            @foreach($results as $res)
                "{{ $res->alternative->name }} (Rank #{{ $res->rank }})",
            @endforeach
        ];

        const dataScores = [
            @foreach($results as $res)
                {{ number_format($res->final_score, 4) }},
            @endforeach
        ];

        const barColors = [
            @foreach($results as $res)
                "#0F3D3A",
            @endforeach
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Preferensi (Vi)',
                    data: dataScores,
                    backgroundColor: barColors,
                    borderRadius: 10,
                    borderWidth: 0,
                    barThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Nilai Akhir V: ' + context.parsed.y.toFixed(4);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1.0,
                        ticks: {
                            stepSize: 0.2,
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '600' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection
