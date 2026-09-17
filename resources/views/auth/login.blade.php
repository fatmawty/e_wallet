@extends('layouts.app')

@section('title', 'Login - SPK E-Wallet')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/20 mb-4">
                <i class="fa-solid fa-wallet text-xl"></i>
            </div>
            <h1 class="font-heading font-extrabold text-2xl text-slate-900">Masuk ke SPK E-Wallet</h1>
            <p class="text-sm text-slate-500 mt-2">Gunakan akun Admin atau Responden.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Ingat saya
            </label>
            <button type="submit" class="w-full px-5 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">Masuk</button>
        </form>
    </div>
</div>
@endsection