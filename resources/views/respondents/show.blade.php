@extends('layouts.app')

@section('title', 'Jawaban Responden - SPK E-Wallet')
@section('page-title', 'Jawaban Responden')
@section('page-subtitle', 'Rincian jawaban kuesioner responden.')

@section('content')
@php
    $importanceByCriterion = $respondent->criterionImportanceRatings->keyBy('criterion_id');
    $ratingsByWalletAndCriterion = $respondent->ratings->keyBy(fn ($rating) => $rating->e_wallet_id . ':' . $rating->criterion_id);
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="font-heading text-xl font-bold text-slate-900">{{ $respondent->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $respondent->institution }} · {{ $respondent->major }} · Angkatan {{ $respondent->cohort_year ?? '-' }}
                · {{ $respondent->created_at?->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('respondents.edit', $respondent) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all">
                <i class="fa-solid fa-pen"></i>
                Edit Profil
            </a>
            <form action="{{ route('respondents.destroy', $respondent) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus data responden {{ addslashes($respondent->name) }}?\n\nSELURUH jawaban kuesioner responden ini juga akan dihapus dan ranking SAW akan diperbarui.\nTindakan ini tidak dapat dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-500 hover:bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all">
                    <i class="fa-solid fa-trash"></i>
                    Hapus
                </button>
            </form>
            <a href="{{ route('respondents.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke responden
            </a>
        </div>
    </div>

    <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-6">
            <h3 class="font-heading text-lg font-bold text-slate-900">Tingkat Kepentingan Kriteria</h3>
            <p class="mt-1 text-xs text-slate-500">Skala jawaban 1–5.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-6 py-4">Kriteria</th>
                        <th class="px-6 py-4">Jawaban</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($criteria as $criterion)
                        <tr>
                            <td class="px-6 py-4 text-slate-700">{{ $criterion->code }} — {{ $criterion->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $importanceByCriterion->get($criterion->id)?->score ?? 'Belum ada jawaban' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-6">
            <h3 class="font-heading text-lg font-bold text-slate-900">Penilaian E-Wallet</h3>
            <p class="mt-1 text-xs text-slate-500">Skala jawaban 1–5 untuk setiap kriteria.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-6 py-4">Kriteria</th>
                        @foreach($wallets as $wallet)
                            <th class="px-6 py-4">{{ $wallet->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($criteria as $criterion)
                        <tr>
                            <td class="px-6 py-4 text-slate-700">{{ $criterion->code }} — {{ $criterion->name }}</td>
                            @foreach($wallets as $wallet)
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $ratingsByWalletAndCriterion->get($wallet->id . ':' . $criterion->id)?->score ?? '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if($respondent->general_feedback)
        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <h3 class="font-heading text-lg font-bold text-slate-900">Ulasan Tambahan</h3>
            <p class="mt-3 whitespace-pre-line text-sm text-slate-700">{{ $respondent->general_feedback }}</p>
        </section>
    @endif
</div>
@endsection
