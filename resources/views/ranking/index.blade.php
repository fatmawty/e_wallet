@extends('layouts.app')

@section('title', 'Hasil Ranking - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Hasil Rekomendasi E-Wallet')
@section('page-subtitle', 'Perangkingan akhir berdasarkan nilai preferensi metode Simple Additive Weighting (SAW). Hasil 100% dinamis dari algoritma.')

@section('content')
<div class="space-y-8">

    {{-- REKOMENDASI TERBAIK (CARD BESAR) --}}
    @if(isset($calcResult['best_alternative']))
        <div class="relative overflow-hidden rounded-3xl shadow-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-8 sm:p-10">
            <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-56 h-56 rounded-full bg-amber-400/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="flex items-start gap-6">
                        <div class="flex-col items-center hidden sm:flex">
                            <div class="text-5xl mb-1">🏆</div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-amber-400">Rank #1</span>
                        </div>

                        <div>
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-extrabold uppercase tracking-wider mb-4">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                Rekomendasi E-Wallet Terbaik (SAW)
                            </span>

                            <h2 class="font-heading font-extrabold text-4xl sm:text-5xl text-white mb-3 tracking-tight">
                                {{ $calcResult['best_alternative']->name }}
                            </h2>

                            <p class="text-slate-300 text-sm leading-relaxed max-w-xl">
                                <strong>{{ $calcResult['best_alternative']->name }}</strong> memperoleh nilai preferensi tertinggi sebesar
                                <span class="text-emerald-400 font-mono font-extrabold text-base px-2 py-0.5 rounded-lg bg-slate-800 border border-slate-700">{{ number_format($calcResult['best_score'], 4) }}</span>
                                berdasarkan bobot kriteria dan penilaian yang digunakan dalam perhitungan metode SAW.
                                Menempati peringkat #1 dari {{ count($calcResult['alternatives']) }} alternatif yang dievaluasi.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-start lg:items-end gap-4 shrink-0">
                        <div class="bg-white/10 backdrop-blur border border-white/20 rounded-3xl p-5 text-center min-w-[160px]">
                            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block mb-2">Nilai Akhir V_i</span>
                            <span class="font-heading font-extrabold text-4xl text-white font-mono block">
                                {{ number_format($calcResult['best_score'], 4) }}
                            </span>
                            <span class="text-xs text-emerald-400 font-bold mt-1 block">Tertinggi dari 4 Alternatif</span>
                        </div>

                        <a href="{{ route('ranking.detail', $calcResult['best_alternative']->id) }}"
                           class="px-6 py-3 rounded-2xl font-bold text-sm text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-lg shadow-amber-400/20 transition-all hover:scale-105 flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass-chart"></i> Lihat Detail Perhitungan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PODIUM RANKING + GRAFIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- RANKING CARDS --}}
        <div class="lg:col-span-5 space-y-4">
            <h3 class="font-heading font-bold text-xl text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-ranking-star text-amber-500"></i> Perangkingan Lengkap
            </h3>

            @php
                $medals = ['🥇', '🥈', '🥉', '4️⃣'];
                $medalBg = [
                    1 => 'border-amber-400 bg-amber-50/70 ring-2 ring-amber-400/20 shadow-md',
                    2 => 'border-slate-300 bg-slate-50',
                    3 => 'border-amber-700/30 bg-orange-50/60',
                    4 => 'border-slate-200 bg-white',
                ];
            @endphp

            @foreach($results as $res)
                <div class="flex items-center justify-between p-5 rounded-3xl border {{ $medalBg[$res->rank] ?? 'border-slate-200 bg-white' }} transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="text-3xl w-10 text-center">{{ $medals[$res->rank - 1] ?? '#' . $res->rank }}</div>

                        <div class="w-11 h-11 rounded-2xl text-white font-bold text-sm flex items-center justify-center shadow-sm shrink-0" style="background-color: {{ $res->alternative->brand_color }}">
                            {{ substr($res->alternative->name, 0, 1) }}
                        </div>

                        <div>
                            <h4 class="font-heading font-bold text-lg text-slate-900 leading-tight">
                                {{ $res->alternative->name }}
                            </h4>
                            <span class="text-xs font-mono font-semibold text-slate-400">{{ $res->alternative->code }}</span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="font-heading font-extrabold text-xl text-slate-900 font-mono block">
                            {{ number_format($res->final_score, 4) }}
                        </span>
                        <a href="{{ route('ranking.detail', $res->alternative->id) }}"
                           class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            Detail &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BAR CHART --}}
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col">
            <div class="mb-6">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Chart.js Visualisasi</span>
                <h3 class="font-heading font-bold text-xl text-slate-900">Grafik Nilai Preferensi SAW (V_i)</h3>
                <p class="text-xs text-slate-500 mt-1">Sumbu Y menunjukkan skor preferensi akhir. Semakin tinggi batang, semakin baik E-Wallet tersebut.</p>
            </div>

            <div class="flex-1 relative min-h-[280px]">
                <canvas id="rankingBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TABEL PERBANDINGAN RINGKASAN --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-heading font-bold text-xl text-slate-900">Tabel Ringkasan Hasil SAW</h3>
            <p class="text-xs text-slate-500">Hasil akhir seluruh alternatif diurutkan berdasarkan nilai V_i dari yang terbesar.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-6">Peringkat</th>
                        <th class="py-4 px-4">E-Wallet</th>
                        @foreach($calcResult['criteria'] as $crit)
                            <th class="py-4 px-4 text-center">{{ $crit->code }} (W={{ number_format($crit->weight, 2) }})</th>
                        @endforeach
                        <th class="py-4 px-6 text-center bg-indigo-50/60 text-indigo-900">Nilai V_i</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    @foreach($results as $res)
                        @php
                            $details = $res->details;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $res->rank == 1 ? 'bg-emerald-50/30' : '' }}">
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 font-heading font-extrabold text-sm {{ $res->rank == 1 ? 'text-amber-600' : 'text-slate-700' }}">
                                    {{ $medals[$res->rank - 1] ?? '' }} #{{ $res->rank }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg text-white font-bold text-xs flex items-center justify-center" style="background-color: {{ $res->alternative->brand_color }}">
                                        {{ substr($res->alternative->name, 0, 1) }}
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $res->alternative->name }}</span>
                                </div>
                            </td>
                            @foreach($calcResult['criteria'] as $crit)
                                <td class="py-4 px-4 text-center font-mono text-xs text-slate-700 font-semibold">
                                    @if($details && isset($details['weighted_scores'][$crit->id]))
                                        {{ number_format($details['weighted_scores'][$crit->id], 4) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                            <td class="py-4 px-6 text-center bg-indigo-50/40">
                                <span class="font-heading font-extrabold text-lg text-indigo-900 font-mono">
                                    {{ number_format($res->final_score, 4) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('ranking.detail', $res->alternative->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 transition-colors">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('rankingBarChart').getContext('2d');

        const labels = [
            @foreach($results->sortBy('rank') as $res)
                "{{ $res->alternative->name }}",
            @endforeach
        ];

        const dataScores = [
            @foreach($results->sortBy('rank') as $res)
                {{ number_format($res->final_score, 4) }},
            @endforeach
        ];

        const barColors = [
            @foreach($results->sortBy('rank') as $res)
                "{{ $res->alternative->brand_color }}",
            @endforeach
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Preferensi V_i',
                    data: dataScores,
                    backgroundColor: barColors,
                    borderRadius: 12,
                    borderWidth: 0,
                    barThickness: 44,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Outfit', weight: 'bold', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        callbacks: {
                            label: function (context) {
                                return ' V_i = ' + context.parsed.y.toFixed(4);
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
                            font: { family: 'Outfit', size: 13, weight: '700' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection
