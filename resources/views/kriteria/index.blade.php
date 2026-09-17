@extends('layouts.app')

@section('title', 'Kriteria Penilaian - SPK SAW')
@section('page_heading', 'Kelola Kriteria Penilaian')
@section('page_subheading', 'Atur kriteria, jenis (Benefit/Cost), dan bobot yang digunakan dalam metode SAW.')

@section('content')

    <!-- Weight Status Card Banner -->
    <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ffffff, {{ $isWeightValid ? '#f0fdf4' : '#fffbe6' }}); border-color: {{ $isWeightValid ? '#bbf7d0' : '#fef08a' }};">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                     style="width: 48px; height: 48px; background-color: {{ $isWeightValid ? '#10b981' : '#f59e0b' }}; font-size: 1.3rem;">
                    <i class="bi {{ $isWeightValid ? 'bi-check-lg' : 'bi-exclamation-triangle' }}"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Status Total Bobot Kriteria</h6>
                    <p class="mb-0 text-muted small">
                        @if($isWeightValid)
                            Total bobot saat ini sudah <strong>1.00 (100%)</strong>. Perhitungan SAW siap dan valid.
                        @else
                            Total bobot saat ini adalah <strong>{{ number_format($totalWeight, 4) }} ({{ number_format($totalWeight * 100, 1) }}%)</strong>. Disarankan total bobot bernilai <strong>1.00 (100%)</strong>.
                        @endif
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="fs-4 fw-extrabold {{ $isWeightValid ? 'text-success' : 'text-warning' }}">
                    {{ number_format($totalWeight * 100, 1) }}%
                </span>
                <button type="button" class="btn btn-primary fw-semibold rounded-3 px-3 py-2 ms-2" data-bs-toggle="modal" data-bs-target="#addCriterionModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kriteria
                </button>
            </div>
        </div>
    </div>

    <!-- Criteria Table Card -->
    <div class="card-custom p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark m-0"><i class="bi bi-table me-2 text-primary"></i>Daftar Kriteria Penilaian</h6>
            <div class="text-muted small">
                <span class="badge badge-benefit me-1"><i class="bi bi-arrow-up-circle me-1"></i>Benefit</span> = Nilai besar lebih baik
                <span class="badge badge-cost ms-2 me-1"><i class="bi bi-arrow-down-circle me-1"></i>Cost</span> = Nilai kecil lebih baik
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Jenis Kriteria</th>
                        <th>Bobot ($w_j$)</th>
                        <th>Persentase</th>
                        <th>Deskripsi / Penjelasan</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($criteria as $item)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $item->code }}</span></td>
                            <td class="fw-bold text-dark">{{ $item->name }}</td>
                            <td>
                                @if($item->type === 'benefit')
                                    <span class="badge badge-benefit" data-bs-toggle="tooltip" title="Benefit: Semakin besar nilainya semakin baik">
                                        <i class="bi bi-graph-up-arrow me-1"></i> BENEFIT
                                    </span>
                                @else
                                    <span class="badge badge-cost" data-bs-toggle="tooltip" title="Cost: Semakin kecil nilainya semakin baik">
                                        <i class="bi bi-graph-down-arrow me-1"></i> COST
                                    </span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">{{ number_format($item->weight, 4) }}</td>
                            <td class="fw-bold text-dark">{{ number_format($item->weight * 100, 1) }}%</td>
                            <td class="text-muted small">{{ $item->description ?? '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary rounded-2 me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCriterionModal{{ $item->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCriterionModal{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCriterionModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('kriteria.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Edit Kriteria {{ $item->code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-3">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Kode Kriteria</label>
                                                <input type="text" name="code" class="form-control rounded-3" value="{{ old('code', $item->code) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kriteria</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $item->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jenis Kriteria</label>
                                                <select name="type" class="form-select rounded-3" required>
                                                    <option value="benefit" {{ $item->type == 'benefit' ? 'selected' : '' }}>Benefit (Semakin besar semakin baik)</option>
                                                    <option value="cost" {{ $item->type == 'cost' ? 'selected' : '' }}>Cost (Semakin kecil semakin baik)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Bobot Kriteria (0.00 - 1.00)</label>
                                                <input type="number" step="0.01" name="weight" class="form-control rounded-3" value="{{ old('weight', $item->weight) }}" min="0" max="1" required>
                                                <small class="text-muted">Contoh: 0.20 untuk 20%</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                <textarea name="description" class="form-control rounded-3" rows="2">{{ old('description', $item->description) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 fw-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteCriterionModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('kriteria.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body text-center p-4">
                                            <div class="text-danger mb-3" style="font-size: 3rem;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Hapus Kriteria {{ $item->code }}?</h5>
                                            <p class="text-muted small mb-4">Tindakan ini akan menghapus kriteria <strong>{{ $item->name }}</strong> beserta data penilaian terkait secara permanen.</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger rounded-3 px-4 fw-bold">Hapus Data</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data kriteria. Klik tombol "Tambah Kriteria" untuk membuat.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3" class="text-end">TOTAL BOBOT:</td>
                        <td class="{{ $isWeightValid ? 'text-success' : 'text-danger' }} fs-6">
                            {{ number_format($totalWeight, 4) }}
                        </td>
                        <td class="{{ $isWeightValid ? 'text-success' : 'text-danger' }} fs-6">
                            {{ number_format($totalWeight * 100, 1) }}%
                        </td>
                        <td colspan="2">
                            @if($isWeightValid)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i> Valid (100%)</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Belum 100%</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Modal Add Criterion -->
    <div class="modal fade" id="addCriterionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('kriteria.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">Tambah Kriteria Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Kriteria</label>
                            <input type="text" name="code" class="form-control rounded-3" placeholder="Contoh: C6" value="{{ old('code') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kriteria</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Promosi & Cashback" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kriteria</label>
                            <select name="type" class="form-select rounded-3" required>
                                <option value="benefit" selected>Benefit (Semakin besar semakin baik)</option>
                                <option value="cost">Cost (Semakin kecil semakin baik)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bobot Kriteria (0.00 - 1.00)</label>
                            <input type="number" step="0.01" name="weight" class="form-control rounded-3" placeholder="Contoh: 0.15" value="{{ old('weight') }}" min="0" max="1" required>
                            <small class="text-muted">Masukkan nilai desimal antara 0 sampai 1.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Penjelasan singkat kriteria...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 fw-bold">Tambah Kriteria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
