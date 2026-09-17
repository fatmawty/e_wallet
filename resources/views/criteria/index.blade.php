@extends('layouts.app')

@section('title', 'Kelola Kriteria - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Kelola Kriteria & Bobot Penilaian')
@section('page-subtitle', 'Tentukan dimensi kriteria, jenis (Benefit/Cost), dan pembobotan untuk perhitungan metode SAW.')

@section('content')
<div class="space-y-6">
    
    <!-- Weight Status Banner -->
    <div class="p-5 rounded-3xl border {{ $isWeightValid ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-amber-50/90 border-amber-200 text-amber-900' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl {{ $isWeightValid ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fa-solid {{ $isWeightValid ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <h4 class="font-heading font-bold text-base">
                    Total Bobot Kriteria Saat Ini: <span class="font-mono text-lg font-extrabold">{{ number_format($totalWeight, 4) }}</span> ({{ number_format($totalWeight * 100, 1) }}%)
                </h4>
                <p class="text-xs {{ $isWeightValid ? 'text-emerald-700' : 'text-amber-800' }} mt-0.5">
                    @if($isWeightValid)
                        Total bobot memenuhi syarat konsistensi SAW (persentase 100% atau 1.0000).
                    @else
                        <strong>Peringatan!</strong> Total bobot kriteria sebaiknya berjumlah persis 1.0000 (100%) agar hasil perangkingan konsisten.
                    @endif
                </p>
            </div>
        </div>

        <button type="button" onclick="openCreateModal()" class="px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-600/20 transition-all shrink-0">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Kriteria
        </button>
    </div>

    <!-- Criteria Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-bold text-lg text-slate-900">Daftar Kriteria SAW (5 Kriteria Utama)</h3>
                <p class="text-xs text-slate-500">Tabel berisi data kriteria, bobot kepentingan, dan tipe Atribut (Benefit / Cost).</p>
            </div>

            <!-- Legend Badge -->
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold" title="Benefit: Semakin besar nilai semakin baik">
                    <i class="fa-solid fa-arrow-up-right-dots"></i> Benefit
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold" title="Cost: Semakin kecil nilai semakin baik">
                    <i class="fa-solid fa-arrow-down-right-dots"></i> Cost
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-4">Kriteria Penilaian</th>
                        <th class="py-4 px-4">Deskripsi / Penjelasan</th>
                        <th class="py-4 px-4 text-center">Jenis Atribut</th>
                        <th class="py-4 px-4 text-center">Bobot (Wj)</th>
                        <th class="py-4 px-4 text-center">Persentase</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium">
                    @foreach($criteria as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600">{{ $c->code }}</td>
                            <td class="py-4 px-4 font-bold text-slate-900">{{ $c->name }}</td>
                            <td class="py-4 px-4 text-xs text-slate-500 max-w-xs">{{ $c->description ?? '-' }}</td>
                            <td class="py-4 px-4 text-center">
                                @if(strtolower($c->type) === 'benefit')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-arrow-up-right-dots text-[10px]"></i> Benefit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-arrow-down-right-dots text-[10px]"></i> Cost
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-mono font-bold text-slate-900 text-base">
                                {{ number_format($c->weight, 2) }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ number_format($c->weight * 100, 0) }}%
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" 
                                            onclick='openEditModal(@json($c))' 
                                            class="p-2 rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 transition-colors"
                                            title="Edit Kriteria">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('criteria.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kriteria ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition-colors" title="Hapus Kriteria">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Explanatory Tooltip Card -->
    <div class="bg-indigo-50/60 rounded-3xl p-6 border border-indigo-100 text-xs text-indigo-950 space-y-2">
        <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-2">
            <i class="fa-solid fa-lightbulb text-indigo-600"></i> Penjelasan Jenis Kriteria Metode SAW
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div class="bg-white p-4 rounded-2xl border border-indigo-100">
                <span class="font-bold text-emerald-700 block mb-1">Kriteria Benefit (Keuntungan)</span>
                <p class="text-slate-600">
                    Nilai kriteria yang <strong>semakin besar nilainya semakin baik</strong>. Contoh: Keamanan, Kemudahan, Promo, Fitur, Top Up, dan Merchant.
                </p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-indigo-100">
                <span class="font-bold text-amber-700 block mb-1">Kriteria Cost (Biaya)</span>
                <p class="text-slate-600">
                    Nilai kriteria yang <strong>semakin kecil nilainya semakin baik</strong>. Contoh: Biaya Transaksi.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- MODAL FORM CREATE / EDIT KRITERIA -->
<div id="criteriaModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 id="modalTitle" class="font-heading font-bold text-xl text-slate-900">Tambah Kriteria Baru</h3>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="criteriaForm" action="{{ route('criteria.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div>
                <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kode Kriteria <span class="text-rose-500">*</span></label>
                <input type="text" name="code" id="code" required placeholder="Contoh: C6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Kriteria <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required placeholder="Contoh: Layanan Pelanggan" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jenis Atribut <span class="text-rose-500">*</span></label>
                    <select name="type" id="type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm bg-white">
                        <option value="benefit">Benefit (Keuntungan)</option>
                        <option value="cost">Cost (Biaya)</option>
                    </select>
                </div>

                <div>
                    <label for="weight" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bobot (0 - 1) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="weight" id="weight" required placeholder="Contoh: 0.20" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" id="description" rows="2" placeholder="Penjelasan kriteria..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Kriteria Baru';
        document.getElementById('criteriaForm').action = "{{ route('criteria.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('code').value = '';
        document.getElementById('name').value = '';
        document.getElementById('type').value = 'benefit';
        document.getElementById('weight').value = '';
        document.getElementById('description').value = '';
        document.getElementById('criteriaModal').classList.remove('hidden');
    }

    function openEditModal(criterion) {
        document.getElementById('modalTitle').innerText = 'Edit Kriteria: ' + criterion.name;
        document.getElementById('criteriaForm').action = "/kriteria/" + criterion.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('code').value = criterion.code;
        document.getElementById('name').value = criterion.name;
        document.getElementById('type').value = criterion.type;
        document.getElementById('weight').value = criterion.weight;
        document.getElementById('description').value = criterion.description || '';
        document.getElementById('criteriaModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('criteriaModal').classList.add('hidden');
    }
</script>
@endsection
