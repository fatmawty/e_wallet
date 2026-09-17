@extends('layouts.app')

@section('title', 'Detail Perhitungan - ' . $alternative->name)
@section('page_heading', 'Detail Perhitungan SAW: ' . $alternative->name)
@section('page_subheading', 'Rincian kontribusi nilai kriteria dan normalisasi untuk e-wallet ' . $alternative->name)

@section('content')

    <!-- Top Summary Card -->
    <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ffffff, #f8fafc); border-color: #e2e8f0;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 text-white d-flex align-items-center justify-content-center shadow-sm"
                     style="width: 60px; height: 60px; background-color: {{ $alternative->brand_color }}; font-size: 1.6rem;">
                    <i class="bi {{ $alternative->logo_icon ?? 'bi-wallet2' }}"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="fw-extrabold text-dark m-0">{{ $alternative->name }}</h3>
                        <span class="badge bg-light text-primary border fw-bold">{{ $alternative->code }}</span>
                    </div>
                    <small class="text-muted">{{ $alternative->description }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 bg-white p-3 rounded-4 border shadow-sm">
                <div>
                    <small class="text-muted fw-semibold d-block">PERINGKAT</small>
                    <span class="fs-4 fw-extrabold text-dark">#{{ $rankResult->rank ?? '-' }}</span>
                </div>
                <div class="vr"></div>
                <div>
                    <small class="text-muted fw-semibold d-block">NILAI AKHIR ($V_i$)</small>
                    <span class="fs-4 fw-extrabold text-success font-monospace">{{ number_format($preference['final_score'], 4) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Matrix Breakdown Card -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-calculator-fill me-2 text-primary"></i>Rincian Nilai per Kriteria Penilaian</h6>
                <small class="text-muted">Tabel kontribusi pembobotan dan normalisasi kriteria untuk {{ $alternative->name }}</small>
            </div>
            <a href="{{ route('hasil-ranking.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold rounded-2">
                &larr; Kembali ke Hasil Ranking
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kriteria Penilaian</th>
                        <th>Jenis Kriteria</th>
                        <th class="text-center">Nilai Asli ($x_{ij}$)</th>
                        <th class="text-center">Target (Max/Min)</th>
                        <th class="text-center">Nilai Normalisasi ($r_{ij}$)</th>
                        <th class="text-center">Bobot ($w_j$)</th>
                        <th class="text-end">Kontribusi ($w_j \cdot r_{ij}$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $crit)
                        @php
                            $item = $preference['breakdown'][$crit->id] ?? null;
                        @endphp
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $crit->code }}</span></td>
                            <td class="fw-bold text-dark">{{ $crit->name }}</td>
                            <td>
                                @if($crit->type == 'benefit')
                                    <span class="badge badge-benefit"><i class="bi bi-arrow-up-circle me-1"></i>BENEFIT</span>
                                @else
                                    <span class="badge badge-cost"><i class="bi bi-arrow-down-circle me-1"></i>COST</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold fs-6">
                                {{ $item['raw_score'] ?? '-' }}
                            </td>
                            <td class="text-center font-monospace">
                                {{ $item['max_min_value'] ?? '-' }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-primary">
                                {{ number_format($item['normalized_score'] ?? 0, 4) }}
                            </td>
                            <td class="text-center font-monospace">
                                {{ number_format($crit->weight, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-extrabold text-success fs-6">
                                {{ number_format($item['weighted_score'] ?? 0, 4) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold fs-6">
                        <td colspan="7" class="text-end">TOTAL NILAI PREFERENSI ($V_i$):</td>
                        <td class="text-end text-success font-monospace fs-5">
                            {{ number_format($preference['final_score'], 4) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Walkthrough Formula Calculation Card -->
    <div class="card-custom p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Penjelasan Langkah Matematis</h6>
        <div class="p-3 bg-light rounded-3 text-secondary small">
            <p class="mb-2"><strong>1. Rumus Normalisasi ($R_{ij}$):</strong></p>
            <ul class="mb-3 ps-3">
                <li>Untuk kriteria <strong>BENEFIT</strong>: $r_{ij} = \frac{x_{ij}}{\max(x)}$ &rarr; Nilai asli dibagi nilai maksimum alternatif pada kriteria tersebut.</li>
                <li>Untuk kriteria <strong>COST</strong>: $r_{ij} = \frac{\min(x)}{x_{ij}}$ &rarr; Nilai minimum alternatif dibagi nilai asli kriteria tersebut.</li>
            </ul>

            <p class="mb-2"><strong>2. Persamaan Total Preferensi ($V_i$):</strong></p>
            <p class="mb-0 font-monospace text-dark fw-bold">
                $V_{ {{ $alternative->code }} } =
                @foreach($criteria as $index => $crit)
                    @php $item = $preference['breakdown'][$crit->id] ?? null; @endphp
                    ({{ number_format($crit->weight, 2) }} \times {{ number_format($item['normalized_score'] ?? 0, 3) }}) {{ $index < $criteria->count() - 1 ? '+' : '' }}
                @endforeach
                = {{ number_format($preference['final_score'], 4) }}
            </p>
        </div>
    </div>

@endsection
