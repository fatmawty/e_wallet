@extends('layouts.app')

@section('title', 'Kelola Kriteria - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Daftar 7 Kriteria & Bobot Penilaian')
@section('page-subtitle', 'Daftar kriteria dan bobot yang digunakan dalam metode SAW.')

@section('content')
<div class="space-y-6">
    
    <!-- Weight Status Banner -->
    <div class="p-5 rounded-3xl border {{ $hasDynamicWeights && $isWeightValid ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-amber-50/90 border-amber-200 text-amber-900' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl {{ $hasDynamicWeights && $isWeightValid ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fa-solid {{ $hasDynamicWeights && $isWeightValid ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <h4 class="font-heading font-bold text-base">
                    @if($hasDynamicWeights)
                        Total Bobot dari {{ $weightRespondentCount }} Responden: <span class="font-mono text-lg font-extrabold">{{ number_format($totalWeight, 4) }}</span> ({{ number_format($totalWeight * 100, 1) }}%)
                    @else
                        Bobot responden belum tersedia
                    @endif
                </h4>
                <p class="text-xs {{ $hasDynamicWeights && $isWeightValid ? 'text-emerald-700' : 'text-amber-800' }} mt-0.5">
                    @if($hasDynamicWeights && $isWeightValid)
                        Bobot dihitung dari jawaban tingkat kepentingan responden dan totalnya 100%.
                    @else
                        Bobot baru dihitung setelah ada responden yang mengisi seluruh tingkat kepentingan dan penilaian e-wallet.
                    @endif
                </p>
            </div>
        </div>

    </div>

    <!-- Criteria Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-bold text-lg text-slate-900">Daftar 7 Kriteria SAW (C1 &ndash; C7)</h3>
                <p class="text-xs text-slate-500">Bobot berasal dari jawaban tingkat kepentingan responden, bukan penilaian admin.</p>
            </div>

            <!-- Legend Badge -->
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold" title="Benefit: Semakin besar nilai semakin baik">
                    <i class="fa-solid fa-arrow-up-right-dots"></i> Benefit (7 Kriteria)
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-4">Kriteria Penilaian</th>
                        <th class="py-4 px-4">Deskripsi / Penjelasan</th>
                        <th class="py-4 px-4 text-center">Jenis Atribut</th>
                        <th class="py-4 px-4 text-center">Bobot (Wj)</th>
                        <th class="py-4 px-6 text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    @foreach($criteria as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600">{{ $c->code }}</td>
                            <td class="py-4 px-4 font-bold text-slate-900">{{ $c->name }}</td>
                            <td class="py-4 px-4 text-xs text-slate-500 max-w-xs">{{ $c->description ?? '-' }}</td>
                            <td class="py-4 px-4 text-center">
                                @if(strtolower($c->type) === 'benefit')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-arrow-up-right-dots text-[10px]"></i> Benefit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-arrow-down-right-dots text-[10px]"></i> Cost
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-mono font-bold text-slate-900 text-base">
                                {{ number_format($c->weight, 6) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                                    {{ number_format($c->weight * 100, 2) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Explanatory Tooltip Card -->
    <div class="bg-indigo-50/60 rounded-3xl p-6 border border-indigo-100 text-xs text-indigo-950 space-y-2">
        <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-2">
            <i class="fa-solid fa-lightbulb text-indigo-600"></i> Sumber Bobot Kriteria
        </h4>
        <p class="text-slate-600 leading-relaxed">
            Setiap responden menilai kepentingan semua kriteria dengan skala 1–5. Sistem menghitung rata-rata jawaban tiap kriteria, kemudian membaginya dengan total seluruh rata-rata untuk memperoleh bobot yang jumlahnya 1. Responden baru diperhitungkan jika seluruh jawaban kepentingan dan penilaian e-wallet lengkap.
        </p>
    </div>

</div>
@endsection
