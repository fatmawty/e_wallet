@extends('layouts.app')

@section('title', 'Edit Responden - SPK E-Wallet')
@section('page-title', 'Edit Data Responden')
@section('page-subtitle', 'Perbarui informasi profil responden. Jawaban kuesioner tidak terpengaruh.')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Info: Jawaban aman --}}
    <div class="flex items-start gap-3 rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-sm text-emerald-800">
        <i class="fa-solid fa-shield-halved text-emerald-500 mt-0.5 text-base shrink-0"></i>
        <span><strong>Jawaban kuesioner aman.</strong> Halaman ini hanya mengubah data profil (nama, institusi, prodi, angkatan, dan ulasan) — tidak menyentuh jawaban kuesioner responden.</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 p-6">
            <h3 class="font-heading font-bold text-xl text-slate-900">
                Edit Profil: {{ $respondent->name }}
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Responden #{{ $respondent->id }} · Diisi pada {{ $respondent->created_at?->format('d/m/Y H:i') }}
            </p>
        </div>

        <form action="{{ route('respondents.update', $respondent) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $respondent->name) }}"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition @error('name') border-red-400 bg-red-50 @enderror"
                       placeholder="Nama lengkap responden">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Universitas --}}
            <div>
                <label for="institution" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Universitas / Institusi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="institution" name="institution"
                       value="{{ old('institution', $respondent->institution) }}"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition @error('institution') border-red-400 bg-red-50 @enderror"
                       placeholder="Nama universitas atau institusi">
                @error('institution')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Program Studi --}}
            <div>
                <label for="major" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Program Studi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="major" name="major"
                       value="{{ old('major', $respondent->major) }}"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition @error('major') border-red-400 bg-red-50 @enderror"
                       placeholder="Program studi / jurusan">
                @error('major')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Angkatan --}}
            <div>
                <label for="cohort_year" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Tahun Angkatan <span class="text-red-500">*</span>
                </label>
                <input type="number" id="cohort_year" name="cohort_year"
                       value="{{ old('cohort_year', $respondent->cohort_year) }}"
                       min="2000" max="{{ now()->year }}"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition @error('cohort_year') border-red-400 bg-red-50 @enderror"
                       placeholder="Contoh: 2022">
                @error('cohort_year')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ulasan / Feedback --}}
            <div>
                <label for="general_feedback" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Ulasan / Masukan Tambahan
                    <span class="font-normal text-slate-400">(opsional)</span>
                </label>
                <textarea id="general_feedback" name="general_feedback" rows="4"
                          class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition resize-none @error('general_feedback') border-red-400 bg-red-50 @enderror"
                          placeholder="Komentar atau masukan tambahan dari responden (opsional)">{{ old('general_feedback', $respondent->general_feedback) }}</textarea>
                @error('general_feedback')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/20 transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('respondents.show', $respondent) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-sm transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
