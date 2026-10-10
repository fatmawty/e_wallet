@extends('layouts.app')

@section('title', 'Detail Evaluasi Likert - ' . $wallet->name)

@section('content')
<div class="hero-gradient py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-semibold">{{ $wallet->name }}</span>
        </nav>

        <!-- Header Profile Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl flex items-center justify-center text-white font-bold text-3xl shadow-xl" style="background-color: {{ $wallet->color_hex }}">
                        {{ substr($wallet->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="font-heading font-extrabold text-3xl text-slate-900">{{ $wallet->name }}</h1>
                            <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm" style="background-color: {{ $wallet->color_hex }}">
                                {{ $wallet->badge_text }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-600 max-w-xl">{{ $wallet->tagline }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="text-center px-4">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Skor Likert</span>
                        <div class="font-heading font-extrabold text-3xl text-slate-900">
                            {{ number_format($avgScore, 2) }}
                            <span class="text-xs font-medium text-slate-400">/ 5.0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            
            <!-- Criterion Breakdown Bars -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                    <h2 class="font-heading font-bold text-xl text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-600"></i>
                        <span>Breakdown Penilaian Per Kriteria</span>
                    </h2>

                    <div class="space-y-6">
                        @foreach($criterionBreakdown as $item)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900">{{ $item['criterion']->name }}</h3>
                                        <p class="text-xs text-slate-500">{{ $item['criterion']->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-heading font-extrabold text-lg text-slate-900">{{ number_format($item['avg_score'], 2) }}</span>
                                        <span class="text-xs text-slate-400">/ 5</span>
                                    </div>
                                </div>

                                <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden mb-3">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ ($item['avg_score'] / 5) * 100 }}%; background-color: {{ $wallet->color_hex }}"></div>
                                </div>

                                <!-- Mini distribution 1-5 -->
                                <div class="grid grid-cols-5 gap-2 text-center text-[10px] pt-2 border-t border-slate-200/60">
                                    @foreach($item['distribution'] as $star => $dist)
                                        <div class="text-slate-500">
                                            <span class="font-semibold text-slate-700">{{ $star }}&star;:</span> {{ $dist['pct'] }}%
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Star Summary Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <h3 class="font-heading font-bold text-lg text-slate-900 mb-4">Distribusi Bintang Overall</h3>
                    
                    <div class="space-y-3 mb-6">
                        @foreach($starCounts as $star => $data)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="w-8 font-semibold text-slate-700 flex items-center gap-1">{{ $star }} <i class="fa-solid fa-star text-amber-400 text-[10px]"></i></span>
                                <div class="flex-grow h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-400 rounded-full" style="width: {{ $data['pct'] }}%"></div>
                                </div>
                                <span class="w-10 text-right text-slate-500 font-medium">{{ $data['pct'] }}%</span>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('kuesioner.create') }}" class="block w-full py-3 px-4 rounded-xl text-xs font-bold text-center text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all">
                        <i class="fa-solid fa-star mr-1.5"></i> Beri Rating {{ $wallet->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
