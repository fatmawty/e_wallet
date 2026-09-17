@extends('layouts.public')

@section('title', 'Form Kuesioner Evaluasi E-Wallet | Skala Likert 1 - 5')

@section('content')
@php
    $criteriaMap = [
        'C1' => [
            'title' => 'KEAMANAN',
            'question' => 'E-wallet ini memberikan rasa aman dalam melindungi data pribadi dan transaksi saya.',
            'description' => 'Keamanan berkaitan dengan sejauh mana e-wallet memberikan perlindungan terhadap akun, data pribadi, dan transaksi pengguna.',
        ],
        'C2' => [
            'title' => 'KEMUDAHAN PENGGUNAAN',
            'question' => 'E-wallet ini mudah dipahami dan digunakan untuk melakukan berbagai transaksi sehari-hari.',
            'description' => 'Kemudahan penggunaan berkaitan dengan seberapa mudah pengguna memahami menu, fitur, dan melakukan transaksi menggunakan e-wallet.',
        ],
        'C3' => [
            'title' => 'PROMO DAN CASHBACK',
            'question' => 'Promo dan cashback yang ditawarkan e-wallet ini menarik dan memberikan manfaat bagi saya.',
            'description' => 'Promo dan cashback berkaitan dengan daya tarik serta manfaat potongan harga, cashback, atau penawaran lain yang diberikan oleh e-wallet.',
        ],
        'C4' => [
            'title' => 'BIAYA TRANSAKSI',
            'question' => 'Biaya transaksi yang dikenakan e-wallet ini terjangkau dan tidak terlalu membebani saya.',
            'description' => 'Biaya transaksi berkaitan dengan keterjangkauan biaya yang dikenakan ketika pengguna melakukan transaksi menggunakan e-wallet.',
        ],
        'C5' => [
            'title' => 'KELENGKAPAN FITUR',
            'question' => 'E-wallet ini memiliki fitur yang lengkap dan dapat memenuhi berbagai kebutuhan transaksi saya.',
            'description' => 'Kelengkapan fitur berkaitan dengan banyaknya dan keberagaman fitur yang tersedia untuk mendukung kebutuhan transaksi pengguna.',
        ],
        'C6' => [
            'title' => 'KEMUDAHAN TOP UP',
            'question' => 'Proses pengisian saldo (top up) pada e-wallet ini mudah dan praktis dilakukan.',
            'description' => 'Kemudahan top up berkaitan dengan kemudahan pengguna dalam mengisi saldo melalui metode top up yang tersedia.',
        ],
        'C7' => [
            'title' => 'BANYAKNYA MERCHANT/LAYANAN',
            'question' => 'E-wallet ini dapat digunakan pada banyak merchant dan layanan yang sesuai dengan kebutuhan saya.',
            'description' => 'Merchant/layanan berkaitan dengan banyaknya tempat, toko, atau layanan yang menerima dan mendukung penggunaan e-wallet.',
        ],
    ];
@endphp

<div class="hero-gradient py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="fa-solid fa-file-signature text-indigo-600"></i> Survei Persepsi & Kepuasan Pengguna
            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight mb-3">
                Kuesioner Evaluasi E-Wallet
            </h1>
            <p class="text-slate-600 text-sm max-w-3xl mx-auto leading-relaxed">
                Mohon luangkan waktu untuk menilai empat e-wallet utama berdasarkan tujuh kriteria yang telah ditetapkan. Setiap pilihan skala bersifat kosong secara default dan hanya diisi sesuai pengalaman Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="p-4 mb-8 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm">
                <div class="font-bold flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Terdapat beberapa kolom yang belum diisi dengan lengkap:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kuesioner.store') }}" method="POST" id="form-kuesioner" class="space-y-8">
            @csrf

            <div class="sticky top-20 z-20 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-sm backdrop-blur" aria-live="polite">
                <div class="mb-2 flex items-center justify-between gap-3 text-xs font-bold text-slate-600">
                    <span>Progress pengisian</span>
                    <span id="questionnaire-progress-label">0% selesai</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="questionnaire-progressbar">
                    <div id="questionnaire-progress" class="relative h-full w-0 overflow-hidden rounded-full bg-linear-to-r from-indigo-600 via-cyan-500 to-emerald-500 transition-all duration-500 ease-out after:absolute after:inset-0 after:-translate-x-full after:bg-white/30 after:content-[''] after:animate-pulse"></div>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap / Inisial <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Contoh: Aditya Pratama" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Status Pekerjaan / Responden <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm bg-white transition-all">
                            <option value="Mahasiswa" {{ old('status') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa (S1/D3/D4/S2)</option>
                            <option value="Pelajar" {{ old('status') == 'Pelajar' ? 'selected' : '' }}>Pelajar SMA / SMK</option>
                            <option value="Alumni / Pekerja" {{ old('status') == 'Alumni / Pekerja' ? 'selected' : '' }}>Alumni / Pekerja / Umum</option>
                        </select>
                    </div>

                    <div>
                        <label for="institution" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Universitas / Instansi <span class="text-rose-500">*</span></label>
                        <input type="text" name="institution" id="institution" required value="{{ old('institution') }}" placeholder="Contoh: Universitas Indonesia / ITB / UGM" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm transition-all">
                    </div>

                    <div>
                        <label for="major" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Program Studi / Jurusan</label>
                        <input type="text" name="major" id="major" value="{{ old('major') }}" placeholder="Contoh: Teknik Informatika / Manajemen" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm transition-all">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">E-Wallet Utama Yang Paling Sering Anda Gunakan <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($wallets as $wallet)
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="primary_e_wallet_id" value="{{ $wallet->id }}" class="peer sr-only" required {{ old('primary_e_wallet_id') == $wallet->id ? 'checked' : '' }}>
                                    <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 ease-out hover:-translate-y-1.5 hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-100/70 hover:bg-indigo-50/40 active:translate-y-0 active:scale-[0.98] peer-checked:border-indigo-600 peer-checked:bg-indigo-50/80 peer-checked:ring-2 peer-checked:ring-indigo-600/30 peer-checked:shadow-lg peer-checked:shadow-indigo-100 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500/50 text-center">
                                        <div class="wallet-badge w-8 h-8 rounded-full text-white font-bold text-xs flex items-center justify-center mx-auto mb-2 shadow-sm transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6 peer-checked:scale-110" data-wallet-color="{{ $wallet->color_hex ?? '#4f46e5' }}">
                                            {{ substr($wallet->name, 0, 1) }}
                                        </div>
                                        <span class="font-heading font-bold text-sm text-slate-900 block transition-colors duration-200 group-hover:text-indigo-700 peer-checked:text-indigo-700">{{ $wallet->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="font-heading font-bold text-xl text-slate-900">Penilaian Kriteria</h2>
                        <p class="text-xs text-slate-500">Setiap kriteria memiliki satu pertanyaan utama dan empat penilaian e-wallet.</p>
                    </div>
                </div>

                <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-wrap gap-2 text-[11px] font-semibold text-slate-700">
                        @foreach([['value' => 1, 'label' => 'Sangat Tidak Setuju'], ['value' => 2, 'label' => 'Tidak Setuju'], ['value' => 3, 'label' => 'Netral'], ['value' => 4, 'label' => 'Setuju'], ['value' => 5, 'label' => 'Sangat Setuju']] as $scale)
                            <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1">{{ $scale['value'] }} = {{ $scale['label'] }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-8">
                    @foreach($criteria as $criterion)
                        @php $detail = $criteriaMap[$criterion->code] ?? ['title' => $criterion->name, 'question' => $criterion->description ?? '', 'description' => $criterion->description ?? '']; @endphp
                        <section class="group/criterion rounded-3xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6 transition-all duration-300 hover:border-indigo-200 hover:bg-indigo-50/30 hover:shadow-lg hover:shadow-indigo-100/40">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-heading font-bold text-sm shrink-0 transition-transform duration-300 group-hover/criterion:rotate-6 group-hover/criterion:scale-105">
                                    {{ $criterion->code }}
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-lg text-slate-900">{{ $detail['title'] }}</h3>
                                </div>
                            </div>

                            <div class="mb-5 rounded-2xl border border-indigo-100 bg-white p-4">
                                <p class="text-base font-semibold text-slate-900">“{{ $detail['question'] }}”</p>
                                <p class="mt-2 text-xs leading-relaxed text-slate-600">{{ $detail['description'] }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                @foreach($wallets as $wallet)
                                    <div class="group/wallet rounded-2xl border border-slate-200 bg-white p-4 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md">
                                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                                            <div class="wallet-badge w-8 h-8 rounded-xl text-white font-bold text-xs flex items-center justify-center transition-transform duration-300 group-hover/wallet:scale-110 group-hover/wallet:rotate-3" data-wallet-color="{{ $wallet->color_hex ?? '#4f46e5' }}">
                                                {{ substr($wallet->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-heading font-bold text-sm text-slate-900">{{ $wallet->name }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-5 gap-2">
                                            @for($score = 1; $score <= 5; $score++)
                                                <label class="group relative block">
                                                    <input type="radio"
                                                           id="rating_{{ $wallet->id }}_{{ $criterion->id }}_{{ $score }}"
                                                           name="ratings[{{ $wallet->id }}][{{ $criterion->id }}]"
                                                           value="{{ $score }}"
                                                           required
                                                           class="peer sr-only"
                                                           {{ old("ratings.{$wallet->id}.{$criterion->id}") == $score ? 'checked' : '' }}>
                                                    <span class="flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 shadow-sm transition-all duration-200 ease-out cursor-pointer hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50/70 hover:text-indigo-700 hover:shadow-md active:translate-y-0 active:scale-95 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:ring-2 peer-checked:ring-indigo-600/20 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                                        {{ $score }}
                                                    </span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        3
                    </div>
                    <div>
                        <h2 class="font-heading font-bold text-xl text-slate-900">Ulasan & Catatan Opsional</h2>
                        <p class="text-xs text-slate-500">Berikan masukan atau pengalaman unik Anda menggunakan e-wallet pilihan Anda.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="general_feedback" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pesan / Alasan Memilih E-Wallet Utama Anda</label>
                    <textarea name="general_feedback" id="general_feedback" rows="3" placeholder="Contoh: Saya paling suka ShopeePay karena promo dan kemudahan transaksinya..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm transition-all">{{ old('general_feedback') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('home') }}" class="px-5 py-3 rounded-xl font-semibold text-slate-600 bg-slate-100/70 shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-slate-200 hover:text-slate-900 hover:shadow-md active:translate-y-0 active:scale-[0.98] text-sm">Batal</a>
                    <button type="submit" id="btn-submit-kuesioner" class="group/submit px-8 py-3.5 rounded-xl font-bold text-white bg-indigo-600 shadow-lg shadow-indigo-600/30 transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-600/30 active:translate-y-0 active:scale-[0.98] text-sm">
                        <i class="fa-solid fa-paper-plane mr-2 transition-transform duration-300 group-hover/submit:translate-x-1 group-hover/submit:-translate-y-0.5"></i> Kirim Penilaian Kuesioner
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-wallet-color]').forEach(function (badge) {
            badge.style.backgroundColor = badge.dataset.walletColor || '#4f46e5';
        });

        const form = document.getElementById('form-kuesioner');
        const progress = document.getElementById('questionnaire-progress');
        const progressBar = document.getElementById('questionnaire-progressbar');
        const progressLabel = document.getElementById('questionnaire-progress-label');
        const submitButton = document.getElementById('btn-submit-kuesioner');

        if (!form || !progress || !progressBar || !progressLabel) return;

        function updateProgress() {
            const requiredRadioNames = [...new Set([...form.querySelectorAll('input[type="radio"][required]')].map(input => input.name))];
            const answeredRadios = requiredRadioNames.filter(name => form.querySelector(`input[name="${CSS.escape(name)}"]:checked`)).length;
            const requiredFields = [...form.querySelectorAll('input[required]:not([type="radio"]), select[required], textarea[required]')];
            const completedFields = requiredFields.filter(field => field.value.trim() !== '').length;
            const total = requiredRadioNames.length + requiredFields.length;
            const completed = answeredRadios + completedFields;
            const percentage = total ? Math.round((completed / total) * 100) : 0;

            progress.style.width = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', percentage);
            progressLabel.textContent = `${percentage}% selesai`;
        }

        form.addEventListener('input', updateProgress);
        form.addEventListener('change', updateProgress);
        form.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.classList.add('cursor-wait', 'opacity-75');
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Menyimpan penilaian...';
        });
        updateProgress();
    });
</script>
@endsection
