@extends('layouts.app')

@section('title', 'Data Responden - SPK E-Wallet')
@section('page-title', 'Data Responden')
@section('page-subtitle', 'Daftar responden yang mengisi kuesioner dan e-wallet utama yang dipilih.')

@section('content')
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-heading font-bold text-xl text-slate-900">Daftar Responden</h3>
        <p class="text-xs text-slate-500 mt-1">Data ini hanya dapat diakses oleh Admin.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-4 px-6">Nama</th>
                    <th class="py-4 px-4">Status</th>
                    <th class="py-4 px-4">Institusi</th>
                    <th class="py-4 px-4">E-Wallet Utama</th>
                    <th class="py-4 px-6">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($respondents as $respondent)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 px-6 font-semibold text-slate-900">{{ $respondent->name }}</td>
                        <td class="py-4 px-4 text-slate-600">{{ $respondent->status }}</td>
                        <td class="py-4 px-4 text-slate-600">{{ $respondent->institution }}</td>
                        <td class="py-4 px-4 font-semibold text-slate-700">{{ $respondent->primaryEWallet?->name ?? '-' }}</td>
                        <td class="py-4 px-6 text-slate-500">{{ $respondent->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-500">Belum ada data responden.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6">{{ $respondents->links() }}</div>
</div>
@endsection