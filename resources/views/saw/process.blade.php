@extends('layouts.app')

@section('title', 'Proses Perhitungan SAW - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Proses Perhitungan Metode SAW')
@section('page-subtitle', 'Transparansi alur matematis Simple Additive Weighting secara bertahap dari pembentukan matriks hingga perangkingan.')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Banner & Trigger Button -->
    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 rounded-3xl p-6 text-white shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-400 block mb-1">Metodologi Transparan</span>
            <h3 class="font-heading font-extrabold text-2xl text-white">Eksekusi Perhitungan SAW Dinamis</h3>
            <p class="text-xs text-slate-300 max-w-xl mt-1 leading-relaxed">
                Tekan tombol di samping untuk mengeksekusi rumus matematis SAW menggunakan data terbaru dari database.
            </p>
        </div>

        <form action="{{ route('saw.calculate') }}" method="POST" class="shrink-0">
            @csrf
            <button type="submit" class="px-6 py-3.5 rounded-2xl font-extrabold text-sm text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xl shadow-amber-400/20 transition-all hover:scale-105">
                <i class="fa-solid fa-calculator mr-2 text-slate-950"></i> PROSES HITUNG SAW
            </button>
        </form>
    </div>

    <!-- STEPPER 7 LANGKAH VISUAL -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 block mb-3">Alur Tahapan Algoritma</span>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 text-center text-xs font-semibold">
            <div class="p-3 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-900">
                <span class="block text-[10px] uppercase font-bold text-indigo-500">Step 1</span>
                <span>Data Kriteria</span>
            </div>
            <div class="p-3 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-900">
                <span class="block text-[10px] uppercase font-bold text-indigo-500">Step 2</span>
                <span>Bobot Kriteria</span>
            </div>
            <div class="p-3 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-900">
                <span class="block text-[10px] uppercase font-bold text-indigo-500">Step 3</span>
                <span>Alternatif</span>
            </div>
            <div class="p-3 rounded-2xl bg-sky-50 border border-sky-100 text-sky-900">
                <span class="block text-[10px] uppercase font-bold text-sky-500">Step 4</span>
                <span>Matriks X_ij</span>
            </div>
            <div class="p-3 rounded-2xl bg-sky-50 border border-sky-100 text-sky-900">
                <span class="block text-[10px] uppercase font-bold text-sky-500">Step 5</span>
                <span>Normalisasi R</span>
            </div>
            <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900">
                <span class="block text-[10px] uppercase font-bold text-emerald-600">Step 6</span>
                <span>Nilai Preferensi V</span>
            </div>
            <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 font-bold">
                <span class="block text-[10px] uppercase font-bold text-amber-600">Step 7</span>
                <span>Perangkingan</span>
            </div>
        </div>
    </div>

    @if($calcResult['has_data'])

        <!-- STEP 1 & 2: KRITERIA DAN BOBOT -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Tahap 1 & 2</span>
                <h3 class="font-heading font-bold text-xl text-slate-900">Kriteria dan Bobot</h3>
                <p class="text-xs text-slate-500 mt-1">Bobot diambil langsung dari tabel criteria dan digunakan tanpa perhitungan ulang.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6">Kode</th>
                            <th class="py-4 px-4">Kriteria</th>
                            <th class="py-4 px-4 text-center">Jenis</th>
                            <th class="py-4 px-6 text-center">Bobot (W)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($calcResult['criteria'] as $crit)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-6 font-mono font-bold text-indigo-700">{{ $crit->code }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $crit->name }}</td>
                                <td class="py-3 px-4 text-center text-xs font-bold uppercase text-slate-600">{{ $crit->type }}</td>
                                <td class="py-3 px-6 text-center font-mono font-bold text-slate-900">{{ number_format($crit->weight, 5) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-sm">
                        <tr>
                            <td colspan="3" class="py-3 px-6 text-right text-slate-700">Total Bobot</td>
                            <td class="py-3 px-6 text-center font-mono text-indigo-700">{{ number_format($calcResult['total_weight'], 5) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- STEP 4: MATRIKS KEPUTUSAN X -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-600">Tahap 4</span>
                    <h3 class="font-heading font-bold text-xl text-slate-900">Matriks Keputusan (X)</h3>
                </div>
                <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">Rata-rata ratings (1 - 5)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6">Alternatif</th>
                            @foreach($calcResult['criteria'] as $crit)
                                <th class="py-4 px-4 text-center">
                                    <span class="font-mono text-indigo-600 block">{{ $crit->code }}</span>
                                    <span>{{ $crit->name }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($calcResult['alternatives'] as $alt)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $alt->brand_color }}"></span>
                                    <span>{{ $alt->name }} ({{ $alt->code }})</span>
                                </td>
                                @foreach($calcResult['criteria'] as $crit)
                                    <td class="py-4 px-4 text-center font-mono font-bold text-slate-800">
                                        {{ $calcResult['matrix_x'][$alt->id][$crit->id] }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold text-xs text-slate-600">
                        <tr>
                            <td class="py-3 px-6 font-bold text-slate-800">Nilai Ekstrem (Max / Min)</td>
                            @foreach($calcResult['criteria'] as $crit)
                                <td class="py-3 px-4 text-center font-mono font-bold text-indigo-700">
                                    @if(strtolower($crit->type) === 'benefit')
                                        Max: {{ $calcResult['extreme_values'][$crit->id]['max'] }}
                                    @else
                                        Min: {{ $calcResult['extreme_values'][$crit->id]['min'] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="p-4 bg-slate-50/60 border-t border-slate-100 text-xs text-slate-500">
                Catatan: Setiap nilai X merupakan rata-rata jawaban kuesioner pada tabel ratings untuk e-wallet dan kriteria terkait.
            </div>
        </div>

        <!-- STEP 5: MATRIKS NORMALISASI R -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-600">Tahap 5</span>
                <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">Matriks Normalisasi (R)</h3>
                
                <!-- Formulas Explanation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-2">
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                        <strong class="block mb-0.5">Rumus Kriteria Benefit:</strong>
                        <code class="font-mono text-emerald-800">r_ij = x_ij / Max(x_ij)</code>
                    </div>
                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900">
                        <strong class="block mb-0.5">Rumus Kriteria Cost:</strong>
                        <code class="font-mono text-amber-800">r_ij = Min(x_ij) / x_ij</code>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6">Alternatif</th>
                            @foreach($calcResult['criteria'] as $crit)
                                <th class="py-4 px-4 text-center">
                                    <span class="font-mono text-indigo-600 block">{{ $crit->code }}</span>
                                    <span class="text-[11px] font-bold text-slate-700">({{ ucfirst($crit->type) }})</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($calcResult['alternatives'] as $alt)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $alt->name }}
                                </td>
                                @foreach($calcResult['criteria'] as $crit)
                                    @php
                                        $rVal = $calcResult['matrix_r'][$alt->id][$crit->id];
                                    @endphp
                                    <td class="py-4 px-4 text-center font-mono">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $rVal == 1.0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-800' }}">
                                            {{ number_format($rVal, 4) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STEP 6: MATRIKS TERBOBOT -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Tahap 6</span>
                <h3 class="font-heading font-bold text-xl text-slate-900">Matriks Terbobot (W × R)</h3>
                <p class="text-xs text-slate-500 mt-1">Setiap nilai normalisasi dikalikan dengan bobot kriterianya.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6">Alternatif</th>
                            @foreach($calcResult['criteria'] as $crit)
                                <th class="py-4 px-4 text-center">
                                    <span class="font-mono text-indigo-600 block">{{ $crit->code }}</span>
                                    <span class="text-[11px]">W × R</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($calcResult['alternatives'] as $alt)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">{{ $alt->name }}</td>
                                @foreach($calcResult['criteria'] as $crit)
                                    <td class="py-4 px-4 text-center font-mono text-slate-800">
                                        {{ number_format($calcResult['weighted_matrix'][$alt->id][$crit->id], 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STEP 6 & 7: NILAI PREFERENSI V & RANKING CARDS -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="mb-6">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Tahap 6 & 7</span>
                <h3 class="font-heading font-bold text-2xl text-slate-900">Perhitungan Nilai Preferensi (V) & Perangkingan</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Rumus: <code class="font-mono bg-slate-100 px-2 py-0.5 rounded text-indigo-700">V_i = ∑ (W_j × R_ij)</code>. Alternatif dengan nilai V_i tertinggi menjadi Peringkat 1.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($calcResult['alternatives'] as $alt)
                    @php
                        $altRank = $calcResult['ranking'][$alt->id];
                        $finalV = $calcResult['final_scores'][$alt->id];
                    @endphp

                    <div class="bg-slate-50/70 rounded-3xl p-5 border {{ $altRank == 1 ? 'border-amber-400 ring-2 ring-amber-400/20 bg-white shadow-md' : 'border-slate-200/90' }} flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-2xl text-white font-bold text-lg flex items-center justify-center shadow-xs" style="background-color: {{ $alt->brand_color }}">
                                    {{ substr($alt->name, 0, 1) }}
                                </div>

                                @if($altRank == 1)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-400 text-slate-950 font-heading font-extrabold text-xs shadow-xs">
                                        <i class="fa-solid fa-crown text-slate-950"></i> Rank #1
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-200 text-slate-700 font-heading font-bold text-xs">
                                        Rank #{{ $altRank }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="font-heading font-bold text-xl text-slate-900 mb-4">{{ $alt->name }}</h4>

                            <!-- Breakdown W * R sum -->
                            <div class="space-y-1.5 text-xs border-t border-b border-slate-200/60 py-3 mb-4">
                                @foreach($calcResult['criteria'] as $crit)
                                    @php
                                        $wR = $calcResult['weighted_matrix'][$alt->id][$crit->id];
                                    @endphp
                                    <div class="flex justify-between text-slate-600 font-mono">
                                        <span>{{ $crit->code }} (w={{ number_format($crit->weight, 2) }}):</span>
                                        <span class="font-bold text-slate-900">{{ number_format($wR, 4) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-2xl border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nilai Preferensi (V)</span>
                            <span class="font-heading font-extrabold text-2xl text-slate-900 font-mono">{{ number_format($finalV, 4) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RANKING DAN REKOMENDASI OTOMATIS -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Hasil Akhir</span>
                <h3 class="font-heading font-bold text-xl text-slate-900">Ranking dan Rekomendasi</h3>
                <p class="text-xs text-slate-500 mt-1">Urutan ditentukan otomatis berdasarkan nilai preferensi tertinggi.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6">Ranking</th>
                            <th class="py-4 px-4">E-Wallet</th>
                            <th class="py-4 px-6 text-right">Nilai Preferensi (V)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($calcResult['alternatives']->sortBy(fn($alt) => $calcResult['ranking'][$alt->id]) as $alt)
                            <tr class="{{ $calcResult['ranking'][$alt->id] === 1 ? 'bg-amber-50/70' : '' }}">
                                <td class="py-4 px-6 font-heading font-extrabold text-slate-900">#{{ $calcResult['ranking'][$alt->id] }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $alt->name }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-indigo-900">{{ number_format($calcResult['final_scores'][$alt->id], 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-emerald-50 border-t border-emerald-100">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Rekomendasi otomatis</span>
                <p class="mt-1 text-lg font-heading font-extrabold text-emerald-950">
                    {{ $calcResult['best_alternative']->name }} dengan nilai {{ number_format($calcResult['best_score'], 4) }}.
                </p>
            </div>
        </div>

    @endif

</div>
@endsection
