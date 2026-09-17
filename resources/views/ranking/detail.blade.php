@extends('layouts.app')

@section('title', 'Detail Perhitungan - ' . $alternative->name . ' | SPK E-Wallet')

@section('page-title', 'Detail Perhitungan SAW: ' . $alternative->name)
@section('page-subtitle', 'Rincian lengkap kontribusi setiap kriteria dalam perhitungan nilai preferensi ' . $alternative->name . '.')

@section('content')
<div class="space-y-8">

    {{-- HEADER PROFILE CARD --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-3xl text-white font-heading font-extrabold text-3xl flex items-center justify-center shadow-xl shrink-0"
                     style="background-color: {{ $alternative->brand_color }}">
                    {{ substr($alternative->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="font-heading font-extrabold text-3xl text-slate-900">{{ $alternative->name }}</h2>
                        <span class="font-mono font-bold text-sm text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                            {{ $alternative->code }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500">{{ $alternative->description }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <div class="text-center px-4 border-r border-slate-200">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Nilai V_i Akhir</span>
                    <span class="font-heading font-extrabold text-3xl text-slate-900 font-mono">
                        {{ number_format($result->final_score, 4) }}
                    </span>
                </div>
                <div class="text-center px-4">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Peringkat</span>
                    <span class="font-heading font-extrabold text-3xl text-slate-900">#{{ $result->rank }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL TABEL PERHITUNGAN PER KRITERIA --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-heading font-bold text-xl text-slate-900 mb-1">Rincian Perhitungan Per Kriteria</h3>
            <p class="text-xs text-slate-500">
                Tabel menampilkan: Nilai Asli (X_ij) → Normalisasi (R_ij) → Bobot (W_j) → Kontribusi Nilai (W_j × R_ij)
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-4">Kriteria</th>
                        <th class="py-4 px-4 text-center">Jenis</th>
                        <th class="py-4 px-4 text-center">Nilai Asli (X_ij)</th>
                        <th class="py-4 px-4 text-center">Normalisasi (R_ij)</th>
                        <th class="py-4 px-4 text-center">Bobot (W_j)</th>
                        <th class="py-4 px-6 text-center bg-indigo-50/80 text-indigo-900">Kontribusi (W×R)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    @php
                        $details = $result->details ?? [];
                        $rawScores = $details['raw_scores'] ?? [];
                        $normalizedScores = $details['normalized_scores'] ?? [];
                        $weightedScores = $details['weighted_scores'] ?? [];
                        $totalV = 0;
                    @endphp

                    @foreach($criteria as $crit)
                        @php
                            $raw = $rawScores[$crit->id] ?? '-';
                            $norm = isset($normalizedScores[$crit->id]) ? number_format($normalizedScores[$crit->id], 4) : '-';
                            $wTimesR = isset($weightedScores[$crit->id]) ? $weightedScores[$crit->id] : 0;
                            $totalV += $wTimesR;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600">{{ $crit->code }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block leading-tight">{{ $crit->name }}</span>
                                <span class="text-xs text-slate-400">{{ $crit->description }}</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if(strtolower($crit->type) === 'benefit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-arrow-up text-[9px]"></i> Benefit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-arrow-down text-[9px]"></i> Cost
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-mono font-bold text-slate-800 text-base">{{ $raw }}</td>
                            <td class="py-4 px-4 text-center font-mono text-slate-700">{{ $norm }}</td>
                            <td class="py-4 px-4 text-center font-mono font-bold text-slate-900">{{ number_format($crit->weight, 2) }}</td>
                            <td class="py-4 px-6 text-center bg-indigo-50/40">
                                <span class="font-mono font-extrabold text-indigo-900 text-base">
                                    {{ number_format($wTimesR, 4) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-900 text-white font-bold">
                        <td colspan="6" class="py-4 px-6 text-right font-heading text-base">
                            V_i = Σ (W_j × R_ij) =
                        </td>
                        <td class="py-4 px-6 text-center bg-indigo-700 text-amber-300 font-mono font-extrabold text-xl">
                            {{ number_format($result->final_score, 4) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- PROGRESS BAR VISUAL CONTRIBUTION --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <h3 class="font-heading font-bold text-xl text-slate-900 mb-6">Kontribusi Tiap Kriteria terhadap Nilai Akhir</h3>

        <div class="space-y-4">
            @foreach($criteria as $crit)
                @php
                    $wTimesR = isset($weightedScores[$crit->id]) ? (float)$weightedScores[$crit->id] : 0;
                    $pct = $result->final_score > 0 ? round(($wTimesR / $result->final_score) * 100, 1) : 0;
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-1.5 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-indigo-600 text-xs">{{ $crit->code }}</span>
                            <span class="font-semibold text-slate-800">{{ $crit->name }}</span>
                            @if(strtolower($crit->type) === 'benefit')
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Benefit</span>
                            @else
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">Cost</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 font-mono text-xs font-bold">
                            <span class="text-slate-600">W×R = {{ number_format($wTimesR, 4) }}</span>
                            <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width: {{ $pct }}%; background-color: {{ $alternative->brand_color }}">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- BOTTOM NAVIGATION --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('ranking.index') }}"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold text-sm text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-xs transition-all">
            <i class="fa-solid fa-arrow-left text-indigo-600"></i> Kembali ke Hasil Ranking
        </a>

        <a href="{{ route('saw.process') }}"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all">
            <i class="fa-solid fa-calculator"></i> Lihat Proses SAW Lengkap
        </a>
    </div>

</div>
@endsection
