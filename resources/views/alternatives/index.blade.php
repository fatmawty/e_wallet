@extends('layouts.app')

@section('title', 'Alternatif E-Wallet - SPK Pemilihan E-Wallet Terbaik')

@section('page-title', 'Alternatif E-Wallet Target')
@section('page-subtitle', 'Kelola 4 alternatif dompet digital utama yang akan dihitung dan dibandingkan dalam sistem SPK SAW.')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="font-heading font-bold text-lg text-slate-900">4 Alternatif E-Wallet Terdaftar</h3>
            <p class="text-xs text-slate-500">Alternatif utama: GoPay, ShopeePay, OVO, dan DANA.</p>
        </div>

        <button type="button" onclick="openCreateAltModal()" class="px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-600/20 transition-all shrink-0">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Alternatif
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($alternatives as $alt)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-all group">
                <div>
                    <!-- Header Icon & Status -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl text-white font-heading font-extrabold text-xl flex items-center justify-center shadow-md" data-brand-color="{{ $alt->brand_color ?? '#0F3D3A' }}">
                            {{ substr($alt->name, 0, 1) }}
                        </div>
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                            {{ $alt->code }}
                        </span>
                    </div>

                    <h4 class="font-heading font-bold text-xl text-slate-900 group-hover:text-indigo-600 transition-colors mb-2">
                        {{ $alt->name }}
                    </h4>

                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        {{ $alt->description ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                data-alt="{{ json_encode($alt, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                class="p-2 rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 transition-colors"
                                title="Edit Alternatif">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>

                        <form action="{{ route('alternatives.destroy', $alt->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus alternatif ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition-colors" title="Hapus Alternatif">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- MODAL FORM ALTERNATIF -->
<div id="altModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 id="altModalTitle" class="font-heading font-bold text-xl text-slate-900">Tambah Alternatif E-Wallet</h3>
            <button type="button" onclick="closeAltModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="altForm" action="{{ route('alternatives.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="altFormMethod" value="POST">

            <div>
                <label for="alt_code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kode Alternatif <span class="text-rose-500">*</span></label>
                <input type="text" name="code" id="alt_code" required placeholder="Contoh: A5" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label for="alt_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama E-Wallet <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="alt_name" required placeholder="Contoh: LinkAja" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label for="brand_color" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Warna Brand (Hex Color)</label>
                <input type="color" name="brand_color" id="brand_color" value="#0F3D3A" class="h-10 w-full rounded-xl border border-slate-200 cursor-pointer">
            </div>

            <div>
                <label for="alt_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" id="alt_description" rows="3" placeholder="Penjelasan alternatif..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-indigo-500 text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAltModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function applyBrandColors() {
        document.querySelectorAll('[data-brand-color]').forEach((element) => {
            element.style.backgroundColor = element.dataset.brandColor || '#0F3D3A';
        });
    }

    document.addEventListener('DOMContentLoaded', applyBrandColors);

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-alt]');
        if (!trigger) return;

        const alt = JSON.parse(trigger.dataset.alt);
        openEditAltModal(alt);
    });

    function openCreateAltModal() {
        const modal = document.getElementById('altModal');
        document.getElementById('altModalTitle').innerText = 'Tambah Alternatif E-Wallet';
        document.getElementById('altForm').action = "{{ route('alternatives.store') }}";
        document.getElementById('altFormMethod').value = 'POST';
        document.getElementById('alt_code').value = '';
        document.getElementById('alt_name').value = '';
        document.getElementById('brand_color').value = '#0F3D3A';
        document.getElementById('alt_description').value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditAltModal(alt) {
        const modal = document.getElementById('altModal');
        document.getElementById('altModalTitle').innerText = 'Edit Alternatif: ' + alt.name;
        document.getElementById('altForm').action = "/alternatif/" + alt.id;
        document.getElementById('altFormMethod').value = 'PUT';
        document.getElementById('alt_code').value = alt.code;
        document.getElementById('alt_name').value = alt.name;
        document.getElementById('brand_color').value = alt.brand_color || '#0F3D3A';
        document.getElementById('alt_description').value = alt.description || '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAltModal() {
        const modal = document.getElementById('altModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
