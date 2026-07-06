@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 glass-panel p-8 sm:p-10 rounded-3xl border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <!-- Decorative Background Gradients -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center">
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">Selamat Datang</span>
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">Profilku AI</h2>
            <p class="mt-2 text-sm text-slate-400">
                Kenali Diri, Temukan Potensi, Rencanakan Masa Depan.
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="rounded-xl space-y-4">
                <div>
                    <label for="email-address" class="block text-sm font-semibold text-slate-400 mb-1.5">Alamat Email Sekolah</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                        placeholder="nama@sekolah.sch.id" value="{{ old('email') }}">
                    @error('email')
                        <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-semibold text-slate-400 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required 
                            class="appearance-none relative block w-full pl-4 pr-10 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200">
                            <!-- Eye icon -->
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Off icon -->
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" 
                        class="h-4 w-4 text-emerald-500 focus:ring-emerald-500/50 border-slate-700 bg-slate-900 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-400">
                        Ingat Saya
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-slate-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-emerald-500 transition shadow-lg shadow-emerald-500/10">
                    Masuk Sekarang
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
