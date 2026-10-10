@extends('layouts.app')

@section('title', 'Tentang Sistem SPK - Pemilihan E-Wallet Terbaik')

@section('page-title', 'Tentang Sistem SPK E-Wallet')
@section('page-subtitle', 'Informasi lengkap mengenai metode, teknologi, dan alur kerja Sistem Pendukung Keputusan ini.')

@section('content')
<div class="space-y-8">

    {{-- INTRO CARD --}}
    <div class="bg-gradient-to-br from-forest-700 via-forest-800 to-forest-950 text-white rounded-3xl p-8 sm:p-10 shadow-lg relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-200 text-xs font-extrabold uppercase tracking-wider mb-5">
                <i class="fa-solid fa-microchip text-indigo-400"></i> Sistem Pendukung Keputusan (SPK)
            </span>

            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white mb-4 leading-tight">
                SPK Pemilihan E-Wallet Terbaik
            </h2>

            <p class="text-slate-300 text-base leading-relaxed">
                Sistem Pendukung Keputusan (SPK) Pemilihan E-Wallet Terbaik merupakan aplikasi berbasis web yang membantu
                pengguna membandingkan beberapa alternatif dompet digital berdasarkan kriteria yang telah ditentukan.
                Sistem ini menggunakan pendekatan ilmiah metode <strong class="text-white">Simple Additive Weighting (SAW)</strong>
                untuk menghasilkan rekomendasi yang objektif dan transparan.
            </p>
        </div>
    </div>

    {{-- METODE + ALUR KERJA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- METODE SAW --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-calculator text-xl"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-xl text-slate-900">Metode SAW</h3>
                    <span class="text-xs text-indigo-600 font-semibold">Simple Additive Weighting</span>
                </div>
            </div>

            <div class="space-y-4 text-sm text-slate-700 leading-relaxed">
                <p>
                    SAW adalah metode pengambilan keputusan multi-kriteria yang menghitung skor akhir setiap alternatif
                    dengan menjumlahkan perkalian bobot kriteria dan nilai normalisasi.
                </p>

                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                        <span class="font-bold text-emerald-800 text-xs uppercase tracking-wider block mb-1">Kriteria Benefit</span>
                        <code class="font-mono text-emerald-900 text-sm">r_ij = x_ij / Max(x_ij)</code>
                        <p class="text-xs text-emerald-700 mt-1">Semakin besar nilainya semakin baik</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
                        <span class="font-bold text-amber-800 text-xs uppercase tracking-wider block mb-1">Kriteria Cost</span>
                        <code class="font-mono text-amber-900 text-sm">r_ij = Min(x_ij) / x_ij</code>
                        <p class="text-xs text-amber-700 mt-1">Semakin kecil nilainya semakin baik</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200">
                        <span class="font-bold text-indigo-800 text-xs uppercase tracking-wider block mb-1">Nilai Preferensi Akhir</span>
                        <code class="font-mono text-indigo-900 text-sm">V_i = Σ (W_j × R_ij)</code>
                        <p class="text-xs text-indigo-700 mt-1">Alternatif dengan V_i tertinggi = Peringkat #1</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGAIMANA SISTEM BEKERJA --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-gears text-xl"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-xl text-slate-900">Bagaimana Sistem Bekerja?</h3>
                    <span class="text-xs text-sky-600 font-semibold">Alur 7 Langkah Algoritma</span>
                </div>
            </div>

            <ol class="space-y-3 text-sm">
                @php
                    $steps = [
                        ['icon' => 'fa-database', 'color' => 'indigo', 'title' => 'Kumpulkan Penilaian Responden', 'desc' => 'Responden memberi skor skala 1-5 pada setiap e-wallet untuk seluruh kriteria.'],
                        ['icon' => 'fa-weight-hanging', 'color' => 'sky', 'title' => 'Hitung Bobot Kriteria', 'desc' => 'Sistem merata-ratakan jawaban tingkat kepentingan responden lalu menormalisasikannya agar total bobot (W_j) sama dengan 1.'],
                        ['icon' => 'fa-table', 'color' => 'violet', 'title' => 'Bentuk Matriks Keputusan', 'desc' => 'Sistem menghitung rata-rata skor responden untuk setiap pasangan alternatif dan kriteria.'],
                        ['icon' => 'fa-divide', 'color' => 'teal', 'title' => 'Normalisasi Matriks', 'desc' => 'Setiap nilai dinormalisasi dengan rumus Benefit (X/Max) atau Cost (Min/X).'],
                        ['icon' => 'fa-multiply', 'color' => 'blue', 'title' => 'Hitung Nilai Preferensi', 'desc' => 'Nilai normalisasi dikalikan bobot, kemudian dijumlahkan untuk mendapat V_i tiap alternatif.'],
                        ['icon' => 'fa-sort', 'color' => 'emerald', 'title' => 'Perangkingan Otomatis', 'desc' => 'Sistem mengurutkan semua alternatif dari nilai V_i tertinggi ke terendah.'],
                        ['icon' => 'fa-trophy', 'color' => 'amber', 'title' => 'Tampilkan Rekomendasi', 'desc' => 'Alternatif dengan nilai V_i tertinggi ditampilkan sebagai rekomendasi e-wallet terbaik.'],
                    ];
                    $colors = [
                        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                        'sky' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                        'violet' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-700'],
                        'teal' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
                        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                        'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                    ];
                @endphp

                @foreach($steps as $i => $step)
                    @php $c = $colors[$step['color']]; @endphp
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            {{ $i + 1 }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-900">{{ $step['title'] }}</span>
                            <p class="text-xs text-slate-500 leading-relaxed mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- ALTERNATIF & KRITERIA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- ALTERNATIF E-WALLET --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <h3 class="font-heading font-bold text-xl text-slate-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-wallet text-amber-500"></i> Alternatif E-Wallet Target
            </h3>

            <div class="space-y-3">
                @php
                    $colorMap = ['DANA' => '#118EEA', 'OVO' => '#4C2A86', 'ShopeePay' => '#EE4D2D', 'GoPay' => '#00AA13'];
                @endphp
                @foreach($alternatives as $alt)
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl text-white font-bold text-sm flex items-center justify-center shadow-xs shrink-0"
                             style="background-color: {{ $alt->brand_color }}">
                            {{ substr($alt->name, 0, 1) }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $alt->name }}</span>
                            <span class="text-xs text-slate-500">{{ $alt->description }}</span>
                        </div>
                        <span class="ml-auto font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg shrink-0">{{ $alt->code }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- KRITERIA EVALUASI --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <h3 class="font-heading font-bold text-xl text-slate-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-indigo-500"></i> Kriteria Evaluasi
            </h3>

            <div class="space-y-3">
                @foreach($criteria as $crit)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-extrabold text-indigo-600 text-xs w-8 shrink-0">{{ $crit->code }}</span>
                            <div>
                                <span class="font-bold text-slate-900 block text-sm">{{ $crit->name }}</span>
                                <span class="text-xs text-slate-500">
                                    @if($hasDynamicWeights)
                                        Bobot: {{ number_format($crit->weight * 100, 2) }}%
                                    @else
                                        Bobot dihitung setelah ada jawaban responden
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if(strtolower($crit->type) === 'benefit')
                            <span class="text-[11px] px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">Benefit</span>
                        @else
                            <span class="text-[11px] px-2.5 py-1 rounded-full font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">Cost</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TEKNOLOGI --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <h3 class="font-heading font-bold text-xl text-slate-900 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-code text-indigo-600"></i> Teknologi yang Digunakan
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center text-xs font-semibold">
            @foreach([
                ['icon' => 'fa-brands fa-laravel', 'label' => 'Laravel 12', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
                ['icon' => 'fa-brands fa-php', 'label' => 'PHP 8.2+', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                ['icon' => 'fa-solid fa-database', 'label' => 'SQLite', 'color' => 'text-sky-600', 'bg' => 'bg-sky-50'],
                ['icon' => 'fa-solid fa-wind', 'label' => 'Tailwind CSS', 'color' => 'text-teal-600', 'bg' => 'bg-teal-50'],
                ['icon' => 'fa-brands fa-js', 'label' => 'JavaScript', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                ['icon' => 'fa-solid fa-chart-bar', 'label' => 'Chart.js', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            ] as $tech)
                <div class="p-4 rounded-2xl {{ $tech['bg'] }} border border-slate-100 flex flex-col items-center gap-2">
                    <i class="{{ $tech['icon'] }} {{ $tech['color'] }} text-2xl"></i>
                    <span class="{{ $tech['color'] }} font-bold">{{ $tech['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
