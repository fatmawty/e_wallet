@extends('layouts.app')

@section('title', 'Penilaian Alternatif - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Penilaian Matrix Alternatif (Skala 1 - 5)')
@section('page-subtitle', 'Masukkan nilai kriteria untuk tiap E-Wallet menggunakan skala penilaian 1 (Tidak Baik) hingga 5 (Sangat Baik).')

@section('content')
<div class="space-y-6">
    
    <!-- Legend Info Banner -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-heading font-bold text-lg text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-400"></i> Skala Evaluasi Nilai Alternatif (1 - 5)
            </h3>
            <span class="text-xs text-slate-500 font-medium">Gunakan Dropdown Select</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-xs">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="font-bold text-slate-800 block mb-0.5">1 &star;</span>
                <span class="text-slate-500 font-medium">Tidak Baik</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="font-bold text-slate-800 block mb-0.5">2 &star;</span>
                <span class="text-slate-500 font-medium">Kurang Baik</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="font-bold text-slate-800 block mb-0.5">3 &star;</span>
                <span class="text-slate-500 font-medium">Cukup</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="font-bold text-indigo-700 block mb-0.5">4 &star;</span>
                <span class="text-indigo-600 font-medium">Baik</span>
            </div>
            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-center">
                <span class="font-bold text-emerald-800 block mb-0.5">5 &star;</span>
                <span class="text-emerald-700 font-bold">Sangat Baik</span>
            </div>
        </div>
    </div>

    <!-- Rating Form & Matrix Table -->
    <form action="{{ route('scores.store') }}" method="POST" id="scoreForm">
        @csrf

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-heading font-bold text-lg text-slate-900">Matriks Keputusan Penilaian (X_ij)</h3>
                    <p class="text-xs text-slate-500">Ubah nilai sesuai persepsi Anda, lalu tekan tombol Simpan untuk memperbarui hasil perangkingan SAW.</p>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('scores.reset') }}" method="POST" class="inline" onsubmit="return confirm('Reset semua nilai ke sampel awal?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                            <i class="fa-solid fa-rotate-left mr-1.5"></i> Reset Sample
                        </button>
                    </form>

                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-600/20 transition-all">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Penilaian
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-4 px-6 min-w-[180px]">Alternatif E-Wallet</th>
                            @foreach($criteria as $crit)
                                <th class="py-4 px-4 text-center">
                                    <span class="block font-mono text-indigo-600 text-[11px]">{{ $crit->code }}</span>
                                    <span class="block text-slate-900 font-bold capitalize">{{ $crit->name }}</span>
                                    <span class="text-[10px] font-semibold {{ strtolower($crit->type) === 'benefit' ? 'text-emerald-600' : 'text-amber-600' }}">
                                        ({{ ucfirst($crit->type) }})
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($alternatives as $alt)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl text-white font-bold text-sm flex items-center justify-center shadow-xs" style="background-color: {{ $alt->brand_color }}">
                                            {{ substr($alt->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block leading-tight">{{ $alt->name }}</span>
                                            <span class="text-xs text-slate-400 font-mono">{{ $alt->code }}</span>
                                        </div>
                                    </div>
                                </td>

                                @foreach($criteria as $crit)
                                    @php
                                        $currentVal = $scores[$alt->id][$crit->id] ?? 3;
                                    @endphp
                                    <td class="py-4 px-4 text-center">
                                        <select name="scores[{{ $alt->id }}][{{ $crit->id }}]" class="w-full max-w-[120px] px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-xs font-semibold bg-white transition-all">
                                            <option value="5" {{ $currentVal == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
                                            <option value="4" {{ $currentVal == 4 ? 'selected' : '' }}>4 - Baik</option>
                                            <option value="3" {{ $currentVal == 3 ? 'selected' : '' }}>3 - Cukup</option>
                                            <option value="2" {{ $currentVal == 2 ? 'selected' : '' }}>2 - Kurang Baik</option>
                                            <option value="1" {{ $currentVal == 1 ? 'selected' : '' }}>1 - Tidak Baik</option>
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                <span>Catatan: Nilai pada tabel ini merupakan matriks awal X_ij yang diolah dalam rumus SAW.</span>
                <button type="submit" class="px-5 py-2 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                    Simpan & Hitung SAW &rarr;
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
