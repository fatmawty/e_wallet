@extends('layouts.app')

@section('title', 'Tentang Sistem - SPK SAW E-Wallet')
@section('page_heading', 'Tentang Sistem SPK Pemilihan E-Wallet')
@section('page_subheading', 'Informasi lengkap metodologi SAW, kriteria, alternatif, dan alur kerja aplikasi.')

@section('content')

    <!-- Overview Banner -->
    <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ffffff, #eff6ff); border-color: #bfdbfe;">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 fw-semibold">Sistem Pendukung Keputusan (SPK)</span>
                <h3 class="fw-extrabold text-dark mb-2">SPK Pemilihan E-Wallet Terbaik</h3>
                <p class="text-secondary leading-relaxed mb-0">
                    Sistem Pendukung Keputusan Pemilihan E-Wallet Terbaik merupakan aplikasi berbasis web yang membantu pengguna membandingkan beberapa alternatif e-wallet berdasarkan kriteria yang telah ditentukan secara objektif, akurat, dan transparan.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="p-3 bg-white rounded-4 border shadow-sm d-inline-block text-start">
                    <small class="text-muted fw-bold d-block">PENGEMBANGAN TUGAS</small>
                    <span class="fw-bold text-dark">Tugas UAS Design SPK</span>
                    <small class="text-muted d-block mt-1">Teknologi: Laravel + MySQL</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Specs Grid -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Metode -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 d-inline-block mb-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-cpu fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Metode Penilaian</h5>
                <h6 class="text-primary fw-bold mb-2">Simple Additive Weighting (SAW)</h6>
                <p class="text-muted small mb-0">
                    Metode SAW sering dikenal dengan istilah penjumlahan terbobot. Konsep dasar SAW adalah mencari penjumlahan terbobot dari rating kinerja pada setiap alternatif pada semua kriteria.
                </p>
            </div>
        </div>

        <!-- Card 2: Alternatif -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="rounded-3 bg-success-subtle text-success p-3 d-inline-block mb-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-phone fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">4 Alternatif E-Wallet</h5>
                <p class="text-muted small mb-2">Objek penilaian yang diuji dalam sistem:</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border"><i class="bi bi-wallet2 text-primary me-1"></i> DANA</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-qr-code-scan text-purple me-1"></i> OVO</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-bag-heart text-danger me-1"></i> ShopeePay</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-credit-card-2-front text-info me-1"></i> GoPay</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Kriteria -->
        <div class="col-12 col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="rounded-3 bg-info-subtle text-info p-3 d-inline-block mb-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-sliders fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">5 Kriteria Penilaian</h5>
                <ul class="text-muted small mb-0 ps-3">
                    <li><strong>C1:</strong> Keamanan (Benefit)</li>
                    <li><strong>C2:</strong> Kemudahan Penggunaan (Benefit)</li>
                    <li><strong>C3:</strong> Keamanan (Benefit)</li>
                    <li><strong>C4:</strong> Biaya Transaksi (Cost)</li>
                    <li><strong>C5:</strong> Fitur (Benefit)</li>
                    <li><strong>C6:</strong> Top Up (Benefit)</li>
                    <li><strong>C7:</strong> Merchant (Benefit)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Step By Step How System Works Card -->
    <div class="card-custom p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Bagaimana Sistem Bekerja?</h5>

        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">1. Input / Update Data Penilaian</div>
                    <p class="text-muted small mb-0">Pengguna memasukkan atau menggunakan data penilaian skala Likert 1–5 untuk tiap alternatif pada setiap kriteria.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">2. Ambil Bobot Kriteria</div>
                    <p class="text-muted small mb-0">Sistem mengambil bobot setiap kriteria ($w_j$) dengan syarat total bobot harus bernilai 1.00 (100%).</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">3. Matriks Keputusan ($X$)</div>
                    <p class="text-muted small mb-0">Sistem membentuk matriks keputusan $X_{ij}$ yang merepresentasikan nilai asli dari seluruh alternatif.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">4. Normalisasi Matriks ($R$)</div>
                    <p class="text-muted small mb-0">Sistem melakukan normalisasi matriks $R_{ij}$ sesuai sifat kriteria Benefit ($x / \max$) atau Cost ($\min / x$).</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">5. Perhitungan Nilai Preferensi ($V$)</div>
                    <p class="text-muted small mb-0">Sistem menghitung nilai akhir preferensi $V_i = \sum (w_j \times r_{ij})$ untuk setiap alternatif e-wallet.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                    <div class="fw-bold text-primary mb-1">6. Perangkingan & Rekomendasi</div>
                    <p class="text-muted small mb-0">Sistem mengurutkan nilai preferensi terbesar ke terkecil dan menampilkan e-wallet dengan nilai tertinggi sebagai rekomendasi terbaik.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
