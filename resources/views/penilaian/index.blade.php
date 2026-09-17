@extends('layouts.app')

@section('title', 'Penilaian Alternatif - SPK SAW')
@section('page_heading', 'Penilaian Alternatif E-Wallet')
@section('page_subheading', 'Masukkan nilai kriteria (skala Likert 1-5) untuk setiap e-wallet alternatif.')

@section('content')

    <!-- Scale Reference Info Banner -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-info-circle-fill text-primary me-2"></i>Panduan Skala Penilaian (1–5)</h6>
                <p class="text-muted small mb-0">Pilih nilai tingkat kesesuaian alternatif terhadap masing-masing kriteria.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-success-subtle text-success px-2 py-2 border"><strong class="me-1">5:</strong> Sangat Baik</span>
                <span class="badge bg-info-subtle text-info px-2 py-2 border"><strong class="me-1">4:</strong> Baik</span>
                <span class="badge bg-secondary-subtle text-secondary px-2 py-2 border"><strong class="me-1">3:</strong> Cukup</span>
                <span class="badge bg-warning-subtle text-warning px-2 py-2 border"><strong class="me-1">2:</strong> Kurang Baik</span>
                <span class="badge bg-danger-subtle text-danger px-2 py-2 border"><strong class="me-1">1:</strong> Tidak Baik</span>
            </div>
        </div>
    </div>

    <!-- Form Score Matrix -->
    <form action="{{ route('penilaian.update') }}" method="POST">
        @csrf

        <div class="card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Matriks Penilaian Alternatif</h6>
                    <small class="text-muted">Gunakan dropdown untuk mengubah nilai secara dinamis.</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="save" class="btn btn-success fw-bold rounded-3 px-3 py-2">
                        <i class="bi bi-floppy-fill me-1"></i> Simpan Penilaian
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">E-Wallet Alternatif</th>
                            @foreach($criteria as $crit)
                                <th class="text-center" style="min-width: 170px;">
                                    <div class="fw-bold text-dark">{{ $crit->code }} - {{ $crit->name }}</div>
                                    <small class="text-muted fw-normal">
                                        @if($crit->type == 'benefit')
                                            <span class="text-success fw-bold">[Benefit]</span>
                                        @else
                                            <span class="text-danger fw-bold">[Cost]</span>
                                        @endif
                                        ({{ number_format($crit->weight * 100, 0) }}%)
                                    </small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alternatives as $alt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                                             style="width: 38px; height: 38px; background-color: {{ $alt->brand_color }}; font-size: 1.1rem;">
                                            <i class="bi {{ $alt->logo_icon ?? 'bi-wallet2' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $alt->name }}</div>
                                            <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">{{ $alt->code }}</span>
                                        </div>
                                    </div>
                                </td>

                                @foreach($criteria as $crit)
                                    @php
                                        $currentScore = $scoreMatrix[$alt->id][$crit->id] ?? 3;
                                    @endphp
                                    <td class="text-center">
                                        <select name="scores[{{ $alt->id }}][{{ $crit->id }}]" class="form-select text-center fw-bold rounded-3 shadow-sm" style="font-size: 0.9rem;">
                                            <option value="5" {{ $currentScore == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
                                            <option value="4" {{ $currentScore == 4 ? 'selected' : '' }}>4 - Baik</option>
                                            <option value="3" {{ $currentScore == 3 ? 'selected' : '' }}>3 - Cukup</option>
                                            <option value="2" {{ $currentScore == 2 ? 'selected' : '' }}>2 - Kurang Baik</option>
                                            <option value="1" {{ $currentScore == 1 ? 'selected' : '' }}>1 - Tidak Baik</option>
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $criteria->count() + 1 }}" class="text-center text-muted py-4">
                                    Belum ada alternatif yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Button Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <button type="button" class="btn btn-outline-warning fw-semibold rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#resetConfirmModal">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Nilai Default Contoh
            </button>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 py-2 shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Simpan Penilaian
                </button>

                <a href="{{ route('proses-saw.index') }}" class="btn btn-success fw-bold rounded-3 px-4 py-2 shadow-sm">
                    <i class="bi bi-calculator me-1"></i> Lanjut Proses Hitung SAW &rarr;
                </a>
            </div>
        </div>
    </form>

    <!-- Modal Reset Confirmation -->
    <div class="modal fade" id="resetConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('penilaian.reset') }}" method="POST">
                    @csrf
                    <div class="modal-body text-center p-4">
                        <div class="text-warning mb-3" style="font-size: 3rem;">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Reset Penilaian ke Benchmark Default?</h5>
                        <p class="text-muted small mb-4">Nilai penilaian e-wallet (GoPay, ShopeePay, OVO, DANA) akan dikembalikan ke nilai default acuan kriteria awal.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning rounded-3 px-4 fw-bold">Ya, Reset Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
