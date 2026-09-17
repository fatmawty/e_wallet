@extends('layouts.app')

@section('title', 'Alternatif E-Wallet - SPK SAW')
@section('page_heading', 'Kelola Alternatif E-Wallet')
@section('page_subheading', 'Daftar alternatif e-wallet yang dievaluasi dalam sistem pendukung keputusan.')

@section('content')

    <!-- Top Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h6 class="fw-bold text-dark m-0"><i class="bi bi-wallet-fill me-2 text-primary"></i>Daftar E-Wallet Alternatif</h6>
            <small class="text-muted">Total {{ $alternatives->count() }} e-wallet terdaftar dalam sistem.</small>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addAlternativeModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Alternatif
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4 mb-4">
        @forelse($alternatives as $item)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-light text-primary border fw-bold px-3 py-1">{{ $item->code }}</span>
                            <span class="badge bg-success-subtle text-success fw-bold">Aktif</span>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-4 d-flex align-items-center justify-content-center text-white shadow-sm"
                                 style="width: 52px; height: 52px; background-color: {{ $item->brand_color }}; font-size: 1.4rem;">
                                <i class="bi {{ $item->logo_icon ?? 'bi-wallet2' }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark m-0">{{ $item->name }}</h5>
                                <small class="text-muted">ID: #{{ $item->id }}</small>
                            </div>
                        </div>

                        <p class="text-muted small mb-3">
                            {{ $item->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted">Brand Color:</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block border" style="width: 16px; height: 16px; background-color: {{ $item->brand_color }};"></span>
                            <code class="small">{{ $item->brand_color }}</code>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary w-50 rounded-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editAlternativeModal{{ $item->id }}">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger w-50 rounded-2"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteAlternativeModal{{ $item->id }}">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editAlternativeModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <form action="{{ route('alternatif.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-bottom-0 pb-0">
                                <h5 class="modal-title fw-bold text-dark">Edit Alternatif {{ $item->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Kode Alternatif</label>
                                    <input type="text" name="code" class="form-control rounded-3" value="{{ old('code', $item->code) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama E-Wallet</label>
                                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $item->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Warna Brand (Hex)</label>
                                    <input type="color" name="brand_color" class="form-control form-control-color w-100 rounded-3" value="{{ old('brand_color', $item->brand_color) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Icon (Bootstrap Icon Class)</label>
                                    <select name="logo_icon" class="form-select rounded-3">
                                        <option value="bi-wallet2" {{ $item->logo_icon == 'bi-wallet2' ? 'selected' : '' }}>Wallet Icon (bi-wallet2)</option>
                                        <option value="bi-qr-code-scan" {{ $item->logo_icon == 'bi-qr-code-scan' ? 'selected' : '' }}>QR Code Icon (bi-qr-code-scan)</option>
                                        <option value="bi-bank" {{ $item->logo_icon == 'bi-bank' ? 'selected' : '' }}>Bank Icon (bi-bank)</option>
                                        <option value="bi-credit-card-2-front" {{ $item->logo_icon == 'bi-credit-card-2-front' ? 'selected' : '' }}>Credit Card Icon (bi-credit-card-2-front)</option>
                                        <option value="bi-currency-dollar" {{ $item->logo_icon == 'bi-currency-dollar' ? 'selected' : '' }}>Dollar Icon (bi-currency-dollar)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Deskripsi Singkat</label>
                                    <textarea name="description" class="form-control rounded-3" rows="3">{{ old('description', $item->description) }}</textarea>
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
            <div class="modal fade" id="deleteAlternativeModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <form action="{{ route('alternatif.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body text-center p-4">
                                <div class="text-danger mb-3" style="font-size: 3rem;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Hapus Alternatif {{ $item->name }}?</h5>
                                <p class="text-muted small mb-4">Tindakan ini akan menghapus e-wallet <strong>{{ $item->name }}</strong> beserta seluruh data penilaiannya.</p>
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
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-phone fs-1 d-block mb-2"></i>
                Belum ada data alternatif e-wallet. Klik tombol "Tambah Alternatif" untuk menambah.
            </div>
        @endforelse
    </div>

    <!-- Modal Add Alternative -->
    <div class="modal fade" id="addAlternativeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('alternatif.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">Tambah Alternatif E-Wallet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Alternatif</label>
                            <input type="text" name="code" class="form-control rounded-3" placeholder="Contoh: A5" value="{{ old('code') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama E-Wallet</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: ShopeePay" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warna Brand (Hex)</label>
                            <input type="color" name="brand_color" class="form-control form-control-color w-100 rounded-3" value="#ee4d2d" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Icon Logo</label>
                            <select name="logo_icon" class="form-select rounded-3">
                                <option value="bi-wallet2" selected>Wallet Icon (bi-wallet2)</option>
                                <option value="bi-qr-code-scan">QR Code Icon (bi-qr-code-scan)</option>
                                <option value="bi-bank">Bank Icon (bi-bank)</option>
                                <option value="bi-credit-card-2-front">Credit Card Icon (bi-credit-card-2-front)</option>
                                <option value="bi-bag-check">Shopping Bag (bi-bag-check)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Deskripsi mengenai layanan e-wallet...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 fw-bold">Tambah Alternatif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
