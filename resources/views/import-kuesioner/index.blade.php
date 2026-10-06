@extends('layouts.app')

@section('title', 'Import Data Kuesioner - SPK E-Wallet')
@section('page-title', 'Import Data Kuesioner CSV')
@section('page-subtitle', 'Upload file CSV hasil Google Form untuk membaca 7 pertanyaan kriteria, mengubah teks ke skala 1-4, dan menghitung bobot SAW secara otomatis.')

@section('content')
<div class="space-y-8">

    {{-- HEADER INFO CARD --}}
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-emerald-950 text-white rounded-3xl p-7 sm:p-9 shadow-lg relative overflow-hidden">
        <div class="absolute -right-16 -bottom-12 w-72 h-72 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-500/20 border border-teal-500/40 text-teal-300 text-xs font-extrabold uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-file-csv text-teal-400"></i>
                    Pembobotan Otomatis Kuesioner Google Form
                </span>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-white mb-2">
                    Import &amp; Kalkulasi Bobot Kriteria
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed max-w-2xl">
                    Sistem secara otomatis membaca 7 pertanyaan kriteria dari CSV Google Form, memetakan ke C1&ndash;C7,
                    mengonversi jawaban teks (&ldquo;sangat penting&rdquo; &rarr; 4, &ldquo;penting&rdquo; &rarr; 3, dll.) menjadi angka,
                    menghitung rata-rata &amp; bobot ternormalisasi, lalu meng-update ranking SAW E-Wallet.
                </p>
            </div>
            <div class="shrink-0 flex flex-col items-start lg:items-end gap-2">
                @if($latestImport)
                    <span class="text-xs text-emerald-400 font-semibold">
                        <i class="fa-solid fa-check-circle mr-1"></i> Import terakhir:
                    </span>
                    <span class="text-white font-bold text-sm">{{ $latestImport->created_at->format('d M Y, H:i') }}</span>
                    <span class="text-slate-400 text-xs">{{ $latestImport->valid_rows }} responden valid &bull; {{ $latestImport->filename }}</span>
                    @if(!empty($latestImport->file_path))
                        <a href="{{ asset($latestImport->file_path) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-300 border border-white/10 hover:bg-white/15">
                            <i class="fa-solid fa-file-csv"></i> Lihat file CSV asli
                        </a>
                    @endif
                @else
                    <span class="text-slate-400 text-sm">Belum ada data kuesioner di-import.</span>
                @endif
            </div>
        </div>
    </div>

    {{-- SUCCESS ALERT BOX WITH RINGKASAN --}}
    @if(session('success_import'))
        @php $info = session('success_import'); @endphp
        <div class="bg-emerald-500/10 border-2 border-emerald-500/30 rounded-3xl p-6 sm:p-8 backdrop-blur-md shadow-xl text-slate-800 space-y-4">
            <div class="flex items-center gap-3 text-emerald-700 font-heading font-extrabold text-xl">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
                <h3>{{ $info['message'] }}</h3>
            </div>

            <p class="text-sm text-slate-600">File <strong class="text-slate-900">{{ $info['filename'] }}</strong> berhasil diproses. Pembobotan kriteria C1&ndash;C7 telah diperbarui di database dan perhitungan SAW telah dijalankan ulang.</p>

            @if(!empty($info['file_path']))
                <a href="{{ asset($info['file_path']) }}" target="_blank" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                    <i class="fa-solid fa-download"></i> Lihat file CSV yang disimpan
                </a>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div class="bg-white p-4 rounded-2xl border border-emerald-200 text-center shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Data Ditemukan</span>
                    <span class="font-heading font-extrabold text-2xl text-slate-900">{{ $info['total_rows'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-emerald-200 text-center shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block mb-1">Data Valid</span>
                    <span class="font-heading font-extrabold text-2xl text-emerald-600">{{ $info['valid_rows'] }}</span>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-rose-200 text-center shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-500 block mb-1">Data Tidak Valid</span>
                    <span class="font-heading font-extrabold text-2xl text-rose-600">{{ $info['invalid_rows'] }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- FORM UPLOAD CSV --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white/60 backdrop-blur-md border border-white/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50">
                <h3 class="font-heading font-bold text-xl text-slate-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-upload text-emerald-600"></i> Form Upload CSV Google Form
                </h3>
                <p class="text-xs text-slate-500 mb-6">Format: .csv &bull; Maksimal 5 MB &bull; Encoding: UTF-8</p>

                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="p-4 mb-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <div class="font-bold flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                            Terdapat Kesalahan:
                        </div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Drag & Drop Upload Area --}}
                    <div id="dropzone"
                         class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/40 transition-all duration-200"
                         onclick="document.getElementById('csv_file').click()">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-emerald-600 mb-3 block"></i>
                        <p class="font-semibold text-slate-700 text-sm">Klik untuk memilih file CSV</p>
                        <p class="text-xs text-slate-500 mt-1">atau drag &amp; drop file ke sini</p>
                        <p id="file-name" class="mt-3 text-xs font-bold text-emerald-700 hidden"></p>
                    </div>

                    <input type="file"
                           id="csv_file"
                           name="csv_file"
                           accept=".csv,text/csv"
                           class="hidden"
                           onchange="onFileSelected(this)">

                    <button type="submit"
                            class="w-full py-3.5 rounded-2xl font-extrabold text-sm text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.01] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-calculator"></i>
                        <span>Import &amp; Hitung Bobot Otomatis</span>
                    </button>
                </form>
            </div>

            {{-- KETENTUAN MAPPING & JAWABAN TEKS --}}
            <div class="bg-white/60 backdrop-blur-md border border-white/80 rounded-3xl p-6 shadow-xl shadow-slate-200/50 space-y-4">
                <h4 class="font-heading font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-teal-600"></i>
                    Ketentuan Pembacaan Kuesioner
                </h4>

                <div class="space-y-3 text-xs text-slate-700">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="font-bold text-slate-900 block mb-1">1. Skala Konversi Jawaban Teks:</span>
                        <div class="grid grid-cols-2 gap-1.5 font-mono">
                            <span class="bg-white px-2 py-1 rounded border border-slate-200">Tidak penting &rarr; <strong>1</strong></span>
                            <span class="bg-white px-2 py-1 rounded border border-slate-200">Cukup penting &rarr; <strong>2</strong></span>
                            <span class="bg-white px-2 py-1 rounded border border-slate-200">Penting &rarr; <strong>3</strong></span>
                            <span class="bg-white px-2 py-1 rounded border border-slate-200">Sangat penting &rarr; <strong>4</strong></span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="font-bold text-slate-900 block mb-1">2. Penanganan Baris &amp; Header:</span>
                        <ul class="list-disc list-inside space-y-1 text-slate-600">
                            <li>Header pengulangan di tengah data diabaikan otomatis.</li>
                            <li>Baris dengan jawaban kosong/invalid ditandai sebagai data tidak valid.</li>
                            <li>Kolom di luar 7 kriteria (seperti Timestamp, Usia, Status) diabaikan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL BOBOT KRITERIA HASIL IMPORT --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white/60 backdrop-blur-md border border-white/80 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-heading font-bold text-xl text-slate-900">Hasil Pembobotan Kriteria (W)</h3>
                        <p class="text-xs text-slate-500">Bobot hasil normalisasi rata-rata skor responden kuesioner.</p>
                    </div>
                    @if($latestImport)
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                            <i class="fa-solid fa-database mr-1"></i> Data Kuesioner Terpasang
                        </span>
                    @endif
                </div>

                {{-- Tabel Rata-rata dan Bobot --}}
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                                <th class="py-3.5 px-4 text-left">Kode</th>
                                <th class="py-3.5 px-4 text-left">Kriteria</th>
                                <th class="py-3.5 px-4 text-center">Jenis</th>
                                <th class="py-3.5 px-4 text-center">Rata-rata</th>
                                <th class="py-3.5 px-4 text-center">Bobot (W)</th>
                                <th class="py-3.5 px-4 text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $importAverages = $latestImport->averages ?? [];
                                $importWeights  = $latestImport->weights ?? [];
                                $totalW         = $latestImport->total_weight ?? $criteria->sum('weight');
                            @endphp

                            @foreach($criteria as $crit)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">{{ $crit->code }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $crit->name }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if(strtolower($crit->type) === 'benefit')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Benefit</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Cost</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700">
                                        {{ isset($importAverages[$crit->code]) ? number_format($importAverages[$crit->code], 4) : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">
                                        {{ isset($importWeights[$crit->code]) ? number_format($importWeights[$crit->code], 6) : number_format($crit->weight, 6) }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @php
                                            $w = isset($importWeights[$crit->code]) ? $importWeights[$crit->code] : $crit->weight;
                                            $pct = $w * 100;
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 w-12 text-right">{{ number_format($pct, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Total Row --}}
                            <tr class="bg-emerald-50 font-bold border-t-2 border-emerald-200">
                                <td colspan="4" class="py-3.5 px-4 text-right text-sm text-slate-800">Total Bobot:</td>
                                <td class="py-3.5 px-4 text-center font-mono font-extrabold text-emerald-800 text-base">
                                    {{ number_format($totalW, 6) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="text-xs font-bold text-emerald-700 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-check-circle"></i> Pas 100% (1.00)
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 7 PERTANYAAN KRITERIA MAPPING REFERENCE --}}
            <div class="bg-white/60 backdrop-blur-md border border-white/80 rounded-3xl p-6 shadow-xl shadow-slate-200/50 space-y-4">
                <h4 class="font-heading font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-600"></i>
                    7 Pertanyaan Header CSV yang Dideteksi Automatic:
                </h4>

                <div class="space-y-2 text-xs">
                    @php
                        $questionDefs = [
                            'C1' => ['name' => 'Keamanan', 'type' => 'Benefit', 'q' => 'Seberapa penting keamanan akun dan transaksi dalam memilih e-wallet?'],
                            'C2' => ['name' => 'Kemudahan Penggunaan', 'type' => 'Benefit', 'q' => 'Seberapa penting kemudahan penggunaan aplikasi dalam memilih e-wallet?'],
                            'C3' => ['name' => 'Promo dan Cashback', 'type' => 'Benefit', 'q' => 'Seberapa penting promo dan cashback dalam memilih e-wallet?'],
                            'C4' => ['name' => 'Biaya Transaksi', 'type' => 'Cost', 'q' => 'Seberapa penting rendahnya biaya transaksi dalam memilih e-wallet?'],
                            'C5' => ['name' => 'Kelengkapan Fitur', 'type' => 'Benefit', 'q' => 'Seberapa penting kelengkapan fitur dalam memilih e-wallet?'],
                            'C6' => ['name' => 'Kemudahan Top Up', 'type' => 'Benefit', 'q' => 'Seberapa penting kemudahan melakukan top up dalam memilih e-wallet?'],
                            'C7' => ['name' => 'Merchant/Layanan', 'type' => 'Benefit', 'q' => 'Seberapa penting banyaknya merchant atau layanan yang menerima e-wallet dalam memilih e-wallet?'],
                        ];
                    @endphp

                    @foreach($questionDefs as $code => $d)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="font-mono font-bold text-indigo-700 mr-2">{{ $code }} ({{ $d['name'] }})</span>
                                <span class="text-slate-600 italic block sm:inline mt-1 sm:mt-0">&ldquo;{{ $d['q'] }}&rdquo;</span>
                            </div>
                            <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase shrink-0 {{ $d['type'] === 'Benefit' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $d['type'] }}
                            </span>
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
    function onFileSelected(input) {
        const fileNameEl = document.getElementById('file-name');
        const dropzone   = document.getElementById('dropzone');

        if (input.files && input.files[0]) {
            const name = input.files[0].name;
            fileNameEl.textContent = '✓ File dipilih: ' + name;
            fileNameEl.classList.remove('hidden');
            dropzone.classList.add('border-emerald-500', 'bg-emerald-50/40');
        }
    }

    const dz = document.getElementById('dropzone');

    dz.addEventListener('dragover', function (e) {
        e.preventDefault();
        dz.classList.add('border-emerald-500', 'bg-emerald-50/40');
    });

    dz.addEventListener('dragleave', function () {
        dz.classList.remove('border-emerald-500', 'bg-emerald-50/40');
    });

    dz.addEventListener('drop', function (e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            document.getElementById('csv_file').files = e.dataTransfer.files;
            onFileSelected({ files: [file] });
        }
    });
</script>
@endsection
