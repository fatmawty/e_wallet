@extends('layouts.app')

@section('title', 'Kelola Kriteria - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Daftar 7 Kriteria & Bobot Penilaian')
@section('page-subtitle', 'Daftar 7 dimensi kriteria SAW. Bobot kriteria dihitung secara otomatis berdasarkan hasil kuesioner CSV Google Forms.')

@section('content')
<div class="space-y-6">
    
    <!-- Weight Status Banner -->
    <div class="p-5 rounded-3xl border {{ $isWeightValid ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-amber-50/90 border-amber-200 text-amber-900' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl {{ $isWeightValid ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fa-solid {{ $isWeightValid ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <h4 class="font-heading font-bold text-base">
                    Total Bobot 7 Kriteria Saat Ini: <span class="font-mono text-lg font-extrabold">{{ number_format($totalWeight, 4) }}</span> ({{ number_format($totalWeight * 100, 1) }}%)
                </h4>
                <p class="text-xs {{ $isWeightValid ? 'text-emerald-700' : 'text-amber-800' }} mt-0.5">
                    @if($isWeightValid)
                        Total bobot memenuhi syarat konsistensi SAW (persentase pas 100% atau 1.0000).
                    @else
                        <strong>Peringatan!</strong> Total bobot kriteria sebaiknya berjumlah persis 1.0000 (100%). Silakan lakukan Import Kuesioner CSV.
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('import.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 active:bg-teal-800 shadow-md shadow-teal-600/20 transition-all shrink-0 flex items-center gap-2">
            <i class="fa-solid fa-file-csv"></i> Import Kuesioner (Hitung Bobot)
        </a>
    </div>

    <!-- Criteria Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-bold text-lg text-slate-900">Daftar 7 Kriteria SAW (C1 &ndash; C7)</h3>
                <p class="text-xs text-slate-500">Nilai bobot di bawah ini diperoleh secara otomatis dari hasil kuesioner CSV responden.</p>
            </div>

            <!-- Legend Badge -->
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold" title="Benefit: Semakin besar nilai semakin baik">
                    <i class="fa-solid fa-arrow-up-right-dots"></i> Benefit (6 Kriteria)
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold" title="Cost: Semakin kecil nilai semakin baik">
                    <i class="fa-solid fa-arrow-down-right-dots"></i> Cost (1 Kriteria)
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
            Sistem SPK menggunakan pembobotan otomatis berbasis kuesioner responden Google Forms.
            Admin tidak perlu menginput bobot secara manual. Untuk memperbarui bobot kriteria, silakan gunakan menu
            <a href="{{ route('import.index') }}" class="font-bold text-indigo-700 underline">Import Data Kuesioner</a>.
        </p>
    </div>

</div>
@endsection
