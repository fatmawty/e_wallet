@extends('layouts.app')

@section('title', 'Data Responden - SPK E-Wallet')
@section('page-title', 'Data Responden')
@section('page-subtitle', 'Daftar responden yang mengisi kuesioner dan rekapitulasi data untuk analisis.')

@section('content')
<div class="space-y-6">

    <!-- Card Header with Download Action -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-bold text-xl text-slate-900">Daftar Responden &amp; Rekapitulasi</h3>
                <p class="text-xs text-slate-500 mt-1">Total responden yang tercatat dalam sistem kuesioner.</p>
            </div>

            <!-- Tombol Download Excel / CSV untuk Uji Validitas SPSS -->
            <a href="{{ route('respondents.export') }}"
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all hover:scale-[1.02] shrink-0">
                <i class="fa-solid fa-file-excel text-base text-emerald-100"></i>
                <span>Download Rekap Excel (Uji Validitas SPSS)</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="py-4 px-6">Nama</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-4">Institusi</th>
                        <th class="py-4 px-4">Program Studi</th>
                        <th class="py-4 px-4 text-center">Angkatan</th>
                        <th class="py-4 px-4">Tanggal</th>
                        <th class="py-4 px-6 text-center">Rincian Jawaban</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($respondents as $respondent)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-900">{{ $respondent->name }}</td>
                            <td class="py-4 px-4 text-slate-600">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    {{ $respondent->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-600">{{ $respondent->institution }}</td>
                            <td class="py-4 px-4 text-slate-600">{{ $respondent->major }}</td>
                            <td class="py-4 px-4 text-center text-slate-600 font-mono font-semibold">{{ $respondent->cohort_year ?? '-' }}</td>
                            <td class="py-4 px-4 text-slate-500 font-mono text-xs">{{ $respondent->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('respondents.show', $respondent) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 border border-indigo-100 px-3.5 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-all">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Lihat Jawaban</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                Belum ada data responden yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100">{{ $respondents->links() }}</div>
    </div>

    <!-- Info Card untuk Peneliti -->
    <div class="bg-indigo-50/60 rounded-3xl p-6 border border-indigo-100 text-xs text-indigo-950 flex items-start gap-4">
        <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shrink-0 mt-0.5">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <h4 class="font-bold text-sm text-indigo-900 mb-1">Panduan Pengolahan Uji Validitas &amp; Reliabilitas (SPSS)</h4>
            <p class="text-slate-600 leading-relaxed">
                Klik tombol <strong>Download Rekap Excel</strong> di atas untuk mengunduh seluruh matriks jawaban responden (Tingkat Kepentingan C1&ndash;C7 dan Penilaian E-Wallet). 
                File CSV yang dihasilkan sudah memiliki format rapi dengan <em>Total Skor</em> yang siap langsung di-<em>copy-paste</em> ke aplikasi <strong>SPSS</strong> atau <strong>Microsoft Excel</strong> untuk uji korelasi Pearson (Bivariate) dan Cronbach&rsquo;s Alpha.
            </p>
        </div>
    </div>

</div>
@endsection