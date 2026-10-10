@extends('layouts.app')

@section('title', 'Hasil Rekomendasi E-Wallet - SPK SAW')
@section('page_heading', 'Hasil Rekomendasi E-Wallet')
@section('page_subheading', 'Hasil akhir pemeringkatan e-wallet terbaik berdasarkan metode Simple Additive Weighting.')

@section('content')

    <!-- Top Recommendation Highlight Card -->
    @if($bestAlternative)
        <div class="card-custom p-4 mb-4 position-relative overflow-hidden"
             style="background: linear-gradient(135deg, #0F3D3A, #082523); color: #ffffff;">
            <div class="row align-items-center">
                <div class="col-12 col-md-8 mb-3 mb-md-0">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white bg-opacity-20 rounded-pill mb-3 text-white fw-bold style-sm" style="font-size: 0.82rem;">
                        <i class="bi bi-trophy-fill text-warning"></i> REKOMENDASI E-WALLET TERBAIK
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-white text-dark d-flex align-items-center justify-content-center shadow"
                             style="width: 64px; height: 64px; font-size: 1.8rem;">
                            <i class="bi {{ $bestAlternative->logo_icon ?? 'bi-wallet2' }}" style="color: {{ $bestAlternative->brand_color }};"></i>
                        </div>
                        <div>
                            <h2 class="fw-extrabold text-white m-0">{{ $bestAlternative->name }}</h2>
                            <div class="text-white opacity-90 fs-5">
                                Nilai Akhir SAW ($V_i$): <strong>{{ number_format($bestScore, 4) }}</strong>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 mb-0 text-white opacity-90 fs-6">
                        <strong>{{ $bestAlternative->name }}</strong> menempati peringkat <strong>#1</strong> berdasarkan hasil perhitungan algoritma Simple Additive Weighting (SAW) dari bobot & nilai kriteria yang digunakan.
                    </p>
                </div>

                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ route('hasil-ranking.detail', $bestAlternative->id) }}" class="btn btn-light btn-lg fw-bold text-success rounded-3 shadow px-4">
                        <i class="bi bi-eye-fill me-1"></i> Lihat Detail Perhitungan
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Ranking Podium / Cards Row -->
    <div class="row g-4 mb-4">
        @foreach($rankings as $res)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card-custom p-4 h-100 position-relative d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            @if($res->rank == 1)
                                <span class="badge badge-rank-1 fs-6">🥇 Rank #1</span>
                            @elseif($res->rank == 2)
                                <span class="badge badge-rank-2 fs-6">🥈 Rank #2</span>
                            @elseif($res->rank == 3)
                                <span class="badge badge-rank-3 fs-6">🥉 Rank #3</span>
                            @else
                                <span class="badge bg-light text-dark border fw-bold fs-6">Rank #{{ $res->rank }}</span>
                            @endif

                            <span class="badge bg-light text-primary border" style="font-size: 0.75rem;">{{ $res->alternative->code }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                                 style="width: 46px; height: 46px; background-color: {{ $res->alternative->brand_color }}; font-size: 1.25rem;">
                                <i class="bi {{ $res->alternative->logo_icon ?? 'bi-wallet2' }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark m-0">{{ $res->alternative->name }}</h5>
                                <small class="text-muted">E-Wallet Alternatif</small>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 text-center">
                            <div class="text-muted small fw-semibold">NILAI PREFERENSI SAW</div>
                            <div class="fs-3 fw-extrabold text-success font-monospace">
                                {{ number_format($res->final_score, 4) }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('hasil-ranking.detail', $res->alternative->id) }}" class="btn btn-outline-primary fw-semibold w-100 rounded-3">
                        <i class="bi bi-search me-1"></i> Rincian Perhitungan
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chart & Table Row -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart -->
        <div class="col-12 col-lg-7">
            <div class="card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Grafik Peringkat Nilai Akhir</h6>
                        <small class="text-muted">Visualisasi skor preferensi e-wallet (SAW)</small>
                    </div>
                    <span class="badge bg-success-subtle text-success border">Bar Chart.js</span>
                </div>

                <div style="height: 300px; position: relative;">
                    <canvas id="rankingChartHasil"></canvas>
                </div>
            </div>
        </div>

        <!-- Ranking Summary Table -->
        <div class="col-12 col-lg-5">
            <div class="card-custom p-4 h-100">
                <div class="mb-3">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-table me-2 text-primary"></i>Tabel Lengkap Perangkingan</h6>
                    <small class="text-muted">Urutan e-wallet dari skor tertinggi ke terendah</small>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Posisi</th>
                                <th>E-Wallet</th>
                                <th class="text-end">Skor Final</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankings as $res)
                                <tr>
                                    <td>
                                        @if($res->rank == 1)
                                            <span class="badge badge-rank-1">#1</span>
                                        @elseif($res->rank == 2)
                                            <span class="badge badge-rank-2">#2</span>
                                        @elseif($res->rank == 3)
                                            <span class="badge badge-rank-3">#3</span>
                                        @else
                                            <span class="badge bg-light text-dark border">#{{ $res->rank }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                                                 style="width: 26px; height: 26px; background-color: {{ $res->alternative->brand_color }}; font-size: 0.75rem;">
                                                <i class="bi {{ $res->alternative->logo_icon ?? 'bi-wallet2' }}"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $res->alternative->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-success font-monospace">
                                        {{ number_format($res->final_score, 4) }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('hasil-ranking.detail', $res->alternative->id) }}" class="btn btn-sm btn-light border text-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rankingChartHasil').getContext('2d');

        const labels = [
            @foreach($rankings as $item)
                "{{ $item->alternative->name }}",
            @endforeach
        ];

        const scores = [
            @foreach($rankings as $item)
                {{ $item->final_score }},
            @endforeach
        ];

        const colors = [
            @foreach($rankings as $item)
                "{{ $item->alternative->brand_color ?? '#0F3D3A' }}",
            @endforeach
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Preferensi (Vi)',
                    data: scores,
                    backgroundColor: colors,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1.0,
                        grid: { color: '#f1f5f9' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
