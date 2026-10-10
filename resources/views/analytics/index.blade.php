@extends('layouts.app')

@section('title', 'Analisis & Matriks Komparatif E-Wallet | Skala Likert')

@section('content')
<div class="hero-gradient py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-50 border border-sky-100 text-sky-700 text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="fa-solid fa-chart-bar text-sky-600"></i> Comparative Analytics Engine
            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight mb-3">
                Matriks Analisis Komparatif
            </h1>
            <p class="text-slate-600 text-sm leading-relaxed">
                Studi mendalam perbandingan skor Skala Likert 1 - 5 antara GoPay, ShopeePay, OVO, dan DANA pada tiap dimensi evaluasi.
            </p>
        </div>

        <!-- Best Wallet per Criterion Highlights -->
        <div class="mb-12">
            <h2 class="font-heading font-bold text-xl text-slate-900 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-medal text-amber-500"></i>
                <span>E-Wallet Terbaik Berdasarkan Kriteria</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bestByCriterion as $item)
                    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                {{ $item['criterion']->name }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-extrabold text-lg text-slate-900">
                                    {{ $item['best_wallet']->name }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded text-white" style="background-color: {{ $item['best_wallet']->color_hex }}">
                                    {{ number_format($item['max_score'], 2) }} / 5.0
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-2xl text-white font-bold text-sm flex items-center justify-center shadow-md" style="background-color: {{ $item['best_wallet']->color_hex }}">
                            {{ substr($item['best_wallet']->name, 0, 1) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Radar Profile Chart -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm mb-12">
            <div class="mb-6">
                <h2 class="font-heading font-bold text-xl text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-area text-emerald-600"></i>
                    <span>Profil Tiap E-Wallet per Kategori</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Rata-rata skor mentah (skala 1-5) di tiap kategori.</p>
            </div>
            <div class="relative h-[420px] sm:h-[520px] w-full max-w-4xl mx-auto">
                <canvas id="walletRadarChart"></canvas>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-12 overflow-x-auto">
            <h2 class="font-heading font-bold text-xl text-slate-900 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-table-cells text-indigo-600"></i>
                <span>Matriks Skor Rata-rata Likert (1.00 - 5.00)</span>
            </h2>

            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-4 rounded-l-xl">E-Wallet Target</th>
                        <th class="py-4 px-3 text-center">Kemudahan UX</th>
                        <th class="py-4 px-3 text-center">Keamanan</th>
                        <th class="py-4 px-3 text-center">Kecepatan</th>
                        <th class="py-4 px-3 text-center">Fitur</th>
                        <th class="py-4 px-3 text-center">Promo & Biaya</th>
                        <th class="py-4 px-3 text-center">Customer CS</th>
                        <th class="py-4 px-4 text-center rounded-r-xl bg-indigo-100/50 text-indigo-950 font-extrabold">Overall Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    @foreach($matrix as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl text-white font-bold text-xs flex items-center justify-center shadow-sm" style="background-color: {{ $row['wallet']->color_hex }}">
                                    {{ substr($row['wallet']->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('ewallet.show', $row['wallet']->slug) }}" class="font-bold text-slate-900 hover:text-indigo-600">
                                        {{ $row['wallet']->name }}
                                    </a>
                                </div>
                            </td>
                            @foreach($criteria as $crit)
                                <td class="py-4 px-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold {{ $row['scores'][$crit->id] >= 4.5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-800' }}">
                                        {{ number_format($row['scores'][$crit->id], 2) }}
                                    </span>
                                </td>
                            @endforeach
                            <td class="py-4 px-4 text-center bg-indigo-50/40">
                                <span class="font-heading font-extrabold text-base text-indigo-900">
                                    {{ number_format($row['overall_avg'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Demographics Breakdown -->
        <div>
            <!-- University Breakdown Table -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <h3 class="font-heading font-bold text-lg text-slate-900 mb-4">Sebaran Kampus Responden</h3>
                <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                    @foreach($institutionBreakdown as $inst)
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-medium text-slate-800">{{ $inst->institution }}</span>
                            <span class="font-bold px-2 py-0.5 rounded bg-indigo-100 text-indigo-800">{{ $inst->count }} Responden</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('walletRadarChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const radarData = @json($radarData);

        new Chart(canvas, {
            type: 'radar',
            data: radarData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            color: '#64748b',
                            backdropColor: 'transparent'
                        },
                        grid: { color: '#cbd5e1' },
                        angleLines: { color: '#e2e8f0' },
                        pointLabels: {
                            color: '#475569',
                            font: { size: 12, weight: '600' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 18 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.dataset.label}: ${context.raw.toFixed(2)}`
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
