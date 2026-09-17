@extends('layouts.app')

@section('title', 'Proses SAW - Perhitungan Algoritma')
@section('page_heading', 'Proses Perhitungan Metode SAW')
@section('page_subheading', 'Langkah-langkah transparan perhitungan Simple Additive Weighting secara matematis dan realistis.')

@push('styles')
<style>
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .stepper-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        min-width: 100px;
        position: relative;
        z-index: 1;
    }

    .stepper-item::before {
        content: "";
        position: absolute;
        top: 18px;
        left: -50%;
        width: 100%;
        height: 3px;
        background-color: var(--card-border);
        z-index: -1;
    }

    .stepper-item:first-child::before {
        content: none;
    }

    .stepper-item.completed .stepper-circle {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-indigo));
        color: #ffffff;
        border-color: var(--primary-blue);
    }

    .stepper-item.completed::before {
        background-color: var(--primary-blue);
    }

    .stepper-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 2px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .stepper-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        text-align: center;
    }
</style>
@endpush

@section('content')

    <!-- Process SAW Button Bar -->
    <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #eff6ff, #ffffff); border-color: #bfdbfe;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 fw-semibold">Proses Otomatis</span>
                <h5 class="fw-bold text-dark m-0">Kalkulasi Ulang Algoritma SAW</h5>
                <p class="text-muted small m-0">Setiap perubahan bobot dan nilai kriteria akan otomatis dihitung ulang tanpa hardcode.</p>
            </div>

            <form action="{{ route('proses-saw.process') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3 px-4 shadow-sm">
                    <i class="bi bi-play-fill me-1"></i> PROSES HITUNG SAW
                </button>
            </form>
        </div>
    </div>

    <!-- Interactive Stepper -->
    <div class="card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-4"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Tahapan Algoritma SAW</h6>
        <div class="stepper-wrapper">
            <div class="stepper-item completed">
                <div class="stepper-circle">1</div>
                <div class="stepper-title">Data Kriteria</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">2</div>
                <div class="stepper-title">Bobot Kriteria</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">3</div>
                <div class="stepper-title">Data Alternatif</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">4</div>
                <div class="stepper-title">Matriks Keputusan</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">5</div>
                <div class="stepper-title">Normalisasi (R)</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">6</div>
                <div class="stepper-title">Nilai Preferensi (V)</div>
            </div>
            <div class="stepper-item completed">
                <div class="stepper-circle">7</div>
                <div class="stepper-title">Ranking</div>
            </div>
        </div>
    </div>

    <!-- STEP 4: Matriks Keputusan (X) -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-matrix me-2 text-primary"></i>STEP 4: Matriks Keputusan ($X_{ij}$)</h6>
                <small class="text-muted">Data pada tabel merupakan nilai awal yang digunakan dalam proses perhitungan SAW.</small>
            </div>
            <span class="badge bg-light text-dark border">Decision Matrix X</span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($sawData['criteria'] as $crit)
                            <th class="text-center">
                                {{ $crit->code }} <br>
                                <span class="fw-normal text-muted" style="font-size: 0.75rem;">{{ $crit->name }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($sawData['alternatives'] as $alt)
                        <tr>
                            <td class="fw-bold text-dark">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                                         style="width: 28px; height: 28px; background-color: {{ $alt->brand_color }}; font-size: 0.8rem;">
                                        <i class="bi {{ $alt->logo_icon ?? 'bi-wallet2' }}"></i>
                                    </div>
                                    <span>{{ $alt->name }} ({{ $alt->code }})</span>
                                </div>
                            </td>
                            @foreach($sawData['criteria'] as $crit)
                                <td class="text-center fw-bold fs-6">
                                    {{ $sawData['matrix_x'][$alt->id][$crit->id] ?? 0 }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td class="fw-bold text-end">Nilai Target (Max / Min):</td>
                        @foreach($sawData['criteria'] as $crit)
                            <td class="text-center fw-bold">
                                @if($crit->type == 'benefit')
                                    <span class="text-success" data-bs-toggle="tooltip" title="Max Value untuk Benefit">
                                        Max: {{ $sawData['max_min'][$crit->id]['value'] ?? 1 }}
                                    </span>
                                @else
                                    <span class="text-danger" data-bs-toggle="tooltip" title="Min Value untuk Cost">
                                        Min: {{ $sawData['max_min'][$crit->id]['value'] ?? 1 }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- STEP 5: Matriks Normalisasi (R) -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-sliders2 me-2 text-primary"></i>STEP 5: Matriks Normalisasi ($R_{ij}$)</h6>
                <small class="text-muted">Hasil pembagian nilai keputusan berdasarkan jenis kriteria (Benefit atau Cost).</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge badge-benefit">Benefit: $r_{ij} = x_{ij} / \max(x)$</span>
                <span class="badge badge-cost">Cost: $r_{ij} = \min(x) / x_{ij}$</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        @foreach($sawData['criteria'] as $crit)
                            <th class="text-center">
                                {{ $crit->code }}
                                @if($crit->type == 'benefit')
                                    <span class="badge badge-benefit ms-1" style="font-size: 0.65rem;">BENEFIT</span>
                                @else
                                    <span class="badge badge-cost ms-1" style="font-size: 0.65rem;">COST</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($sawData['alternatives'] as $alt)
                        <tr>
                            <td class="fw-bold text-dark">{{ $alt->name }}</td>
                            @foreach($sawData['criteria'] as $crit)
                                <td class="text-center font-monospace fw-bold text-primary">
                                    {{ number_format($sawData['matrix_r'][$alt->id][$crit->id] ?? 0, 4) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 6: Nilai Preferensi (V) Breakdown -->
    <div class="card-custom p-4 mb-4">
        <div class="mb-3">
            <h6 class="fw-bold text-dark m-0"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>STEP 6: Perhitungan Nilai Preferensi ($V_i$)</h6>
            <small class="text-muted">Perhitungan total preferensi $V_i = \sum (w_j \times r_{ij})$ untuk setiap alternatif e-wallet.</small>
        </div>

        <div class="row g-3">
            @foreach($sawData['alternatives'] as $alt)
                @php
                    $pref = $sawData['preferences'][$alt->id] ?? null;
                @endphp
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border rounded-4 p-3 h-100 bg-light-subtle">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                                     style="width: 32px; height: 32px; background-color: {{ $alt->brand_color }}; font-size: 0.9rem;">
                                    <i class="bi {{ $alt->logo_icon ?? 'bi-wallet2' }}"></i>
                                </div>
                                <span class="fw-extrabold text-dark fs-6">{{ $alt->name }}</span>
                            </div>
                            <span class="badge bg-light text-primary border">{{ $alt->code }}</span>
                        </div>

                        <div class="mb-3">
                            @if($pref)
                                @foreach($sawData['criteria'] as $crit)
                                    @php
                                        $item = $pref['breakdown'][$crit->id] ?? null;
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-1 font-monospace" style="font-size: 0.82rem;">
                                        <span class="text-muted">{{ $crit->code }} (w={{ number_format($crit->weight, 2) }}):</span>
                                        <span class="fw-bold text-dark">
                                            {{ number_format($item['normalized_score'] ?? 0, 3) }} &times; {{ number_format($crit->weight, 2) }} =
                                            <span class="text-primary">{{ number_format($item['weighted_score'] ?? 0, 4) }}</span>
                                        </span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">TOTAL ($V_i$):</span>
                            <span class="fs-5 fw-extrabold text-success font-monospace">
                                {{ number_format($pref['final_score'] ?? 0, 4) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- STEP 7: Hasil Ranking Table & Button -->
    <div class="card-custom p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-trophy-fill me-2 text-warning"></i>STEP 7: Hasil Perangkingan Akhir</h6>
                <small class="text-muted">Alternatif diurutkan berdasarkan Nilai Preferensi ($V_i$) dari tertinggi ke terendah.</small>
            </div>
            <a href="{{ route('hasil-ranking.index') }}" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-award-fill me-1"></i> Halaman Hasil Ranking &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">Ranking</th>
                        <th>Kode</th>
                        <th>Nama E-Wallet</th>
                        <th class="text-end">Nilai Preferensi ($V_i$)</th>
                        <th class="text-center">Status Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sawData['rankings'] as $res)
                        <tr>
                            <td>
                                @if($res->rank == 1)
                                    <span class="badge badge-rank-1">🥇 Rank #1</span>
                                @elseif($res->rank == 2)
                                    <span class="badge badge-rank-2">🥈 Rank #2</span>
                                @elseif($res->rank == 3)
                                    <span class="badge badge-rank-3">🥉 Rank #3</span>
                                @else
                                    <span class="badge bg-light text-dark border">Rank #{{ $res->rank }}</span>
                                @endif
                            </td>
                            <td><span class="fw-bold text-primary">{{ $res->alternative->code }}</span></td>
                            <td class="fw-bold text-dark fs-6">{{ $res->alternative->name }}</td>
                            <td class="text-end fw-extrabold fs-5 text-success font-monospace">
                                {{ number_format($res->final_score, 4) }}
                            </td>
                            <td class="text-center">
                                @if($res->rank == 1)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 fw-bold rounded-pill">
                                        <i class="bi bi-star-fill me-1"></i> Rekomendasi Terbaik
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-1 fw-normal">Pertimbangan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
